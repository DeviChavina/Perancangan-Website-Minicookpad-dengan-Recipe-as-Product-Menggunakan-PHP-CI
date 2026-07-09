<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ChefFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }
        $role = $session->get('user_role');
        // CHEF_UNVERIFIED juga boleh masuk dashboard & buat resep
        if (!in_array($role, ['CHEF_UNVERIFIED','CHEF_VERIFIED','ADMIN'])) {
            return redirect()->to('/chef/verify')->with('error', 'Anda perlu menjadi Chef terlebih dahulu');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
