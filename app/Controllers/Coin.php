<?php
namespace App\Controllers;

use App\Models\CoinPackageModel;
use App\Models\CoinTopupModel;
use App\Models\CoinTransactionModel;
use App\Models\UserModel;

class Coin extends BaseController
{
    protected CoinPackageModel     $packageModel;
    protected CoinTopupModel       $topupModel;
    protected UserModel            $userModel;

    public function __construct()
    {
        helper(['cookpad','form','url']);
        $this->packageModel = new CoinPackageModel();
        $this->topupModel   = new CoinTopupModel();
        $this->userModel    = new UserModel();
    }

    /** Halaman beli koin */
    public function store()
    {
        $packages = $this->packageModel->getActive();
        $userId   = session()->get('user_id');
        $user     = $this->userModel->find($userId);

        // Kalau masih ada pending topup, arahkan ke sana
        $pending = $this->topupModel->getPendingByUser($userId);

        return view('coin/store', [
            'packages' => $packages,
            'user'     => $user,
            'pending'  => $pending,
            'title'    => 'Beli Koin - Mini Cookpad',
        ]);
    }

    /** Proses pembelian koin */
    public function buy()
    {
        $rules = [
            'package_id' => 'required|integer',
            'method'     => 'required|in_list[qris,bca_va,mandiri_va,bri_va]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userId    = session()->get('user_id');
        $packageId = (int) $this->request->getPost('package_id');
        $method    = $this->request->getPost('method');

        $package = $this->packageModel->find($packageId);
        if (!$package || !$package['is_active']) {
            return redirect()->back()->with('error', 'Paket tidak valid');
        }

        // Cek apakah ada topup pending aktif
        $pending = $this->topupModel->getPendingByUser($userId);
        if ($pending) {
            return redirect()->to('/coin/pay/' . $pending['id'])
                ->with('info', 'Selesaikan pembayaran sebelumnya terlebih dahulu.');
        }

        $code = match($method) {
            'qris'        => 'QRIS-' . strtoupper(bin2hex(random_bytes(4))),
            'bca_va'      => '8800' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
            'mandiri_va'  => '8900' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
            'bri_va'      => '8700' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
            default       => bin2hex(random_bytes(8)),
        };

        $totalCoins = CoinPackageModel::totalCoins($package);

        $topupId = $this->topupModel->insert([
            'user_id'     => $userId,
            'package_id'  => $packageId,
            'coin_amount' => $totalCoins,
            'price_idr'   => $package['price_idr'],
            'method'      => $method,
            'status'      => 'pending',
            'payment_code'=> $code,
            'expires_at'  => date('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);

        if (!$topupId) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        return redirect()->to('/coin/pay/' . $topupId)->with('success', 'Silakan selesaikan pembayaran.');
    }

    /** Halaman detail pembayaran koin */
    public function pay($id = null)
    {
        $id = (int) $id;
        $topup = $this->topupModel->getWithPackage($id);
        if (!$topup || (int)$topup['user_id'] !== (int)session()->get('user_id')) {
            return redirect()->to('/coin/store')->with('error', 'Transaksi tidak ditemukan');
        }

        if ($topup['status'] === 'paid') {
            return redirect()->to('/coin/store')->with('info', 'Pembayaran sudah selesai.');
        }
        if (strtotime($topup['expires_at']) < time()) {
            $this->topupModel->update($id, ['status' => 'expired']);
            return redirect()->to('/coin/store')->with('error', 'Pembayaran sudah kadaluarsa.');
        }

        return view('coin/pay', [
            'topup' => $topup,
            'title' => 'Pembayaran Koin - Mini Cookpad',
        ]);
    }

    /** Simulasi pembayaran berhasil */
    public function simulate($id = null)
    {
        $id = (int) $id;
        $topup = $this->topupModel->getWithPackage($id);

        if (!$topup || (int)$topup['user_id'] !== (int)session()->get('user_id')) {
            return redirect()->to('/coin/store')->with('error', 'Transaksi tidak ditemukan');
        }
        if ($topup['status'] === 'paid') {
            return redirect()->to('/coin/store')->with('info', 'Sudah dibayar sebelumnya.');
        }
        if (strtotime($topup['expires_at']) < time()) {
            $this->topupModel->update($id, ['status' => 'expired']);
            return redirect()->to('/coin/store')->with('error', 'Pembayaran kadaluarsa.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $userId     = (int) $topup['user_id'];
            $coinAmount = (int) $topup['coin_amount'];

            // 1. Update status topup
            $this->topupModel->update($id, ['status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')]);

            // 2. Tambah koin ke user
            $newBalance = $this->userModel->addCoins($userId, $coinAmount);

            // 3. Catat transaksi koin
            CoinTransactionModel::record($db, [
                'user_id'      => $userId,
                'type'         => 'topup',
                'amount'       => $coinAmount,
                'balance_after'=> $newBalance,
                'ref_table'    => 'coin_topups',
                'ref_id'       => $id,
                'note'         => 'Top-up koin: ' . $topup['package_name'],
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memproses pembayaran');
            }

            // Update session balance
            session()->set('user_coins', $newBalance);

            return redirect()->to('/coin/store')
                ->with('success', "✅ Pembayaran berhasil! +{$coinAmount} koin sudah masuk ke dompet Anda.");

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Coin topup error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }
    }

    /** Halaman riwayat transaksi koin */
    public function history()
    {
        $userId = session()->get('user_id');
        $db     = \Config\Database::connect();

        $transactions = $db->table('coin_transactions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $user = $this->userModel->find($userId);

        return view('coin/history', [
            'transactions' => $transactions,
            'user'         => $user,
            'title'        => 'Riwayat Koin - Mini Cookpad',
        ]);
    }
}
