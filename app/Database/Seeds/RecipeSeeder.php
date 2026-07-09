<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // ─── Chef Verifications ───────────────────────────────────────────
        $chefVerifications = [
            [
                'user_id'          => 2,
                'id_card_number'   => '3201010101010001',
                'id_card_photo'    => 'uploads/ids/chef_rina_id.jpg',
                'certificate_photo' => 'uploads/certificates/chef_rina_cert.jpg',
                'portfolio_url'    => 'https://portfolio.chefrina.id',
                'specialization'   => 'Masakan Indonesia',
                'experience'       => '15 tahun pengalaman di dapur Indonesia autentik, pernah bekerja di beberapa restoran ternama di Jakarta dan Padang.',
                'status'           => 'approved',
                'admin_note'       => 'Dokumen lengkap dan valid. Pengalaman memadai.',
                'reviewed_at'      => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'user_id'          => 3,
                'id_card_number'   => '3201010101010002',
                'id_card_photo'    => 'uploads/ids/chef_takeshi_id.jpg',
                'certificate_photo' => 'uploads/certificates/chef_takeshi_cert.jpg',
                'portfolio_url'    => 'https://portfolio.cheftakeshi.jp',
                'specialization'   => 'Japanese Cuisine',
                'experience'       => '10 tahun spesialisasi ramen dan sushi, lulusan Tokyo Culinary Academy.',
                'status'           => 'approved',
                'admin_note'       => 'Sertifikat kuliner Jepang terverifikasi.',
                'reviewed_at'      => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'user_id'          => 4,
                'id_card_number'   => '3201010101010003',
                'id_card_photo'    => 'uploads/ids/chef_marco_id.jpg',
                'certificate_photo' => 'uploads/certificates/chef_marco_cert.jpg',
                'portfolio_url'    => 'https://portfolio.chefmarco.it',
                'specialization'   => 'Italian Cuisine',
                'experience'       => '8 tahun memasak hidangan Italia otentik, belajar langsung dari Nonna di Tuscany.',
                'status'           => 'approved',
                'admin_note'       => 'Portofolio sangat impresif, resep autentik.',
                'reviewed_at'      => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];

        $this->db->table('chef_verifications')->insertBatch($chefVerifications);

        // ─── Recipes ──────────────────────────────────────────────────────
        $recipes = [
            [
                'chef_id'      => 2,
                'title'        => 'Rendang Padang Asli',
                'slug'         => 'rendang-padang-asli',
                'description'  => 'Rendang Padang asli dengan bumbu rempah yang kaya dan proses memasak perlahan hingga kering. Hidangan ikonik Minangkabau yang wajib dicoba.',
                'cuisine'      => 'Indonesian',
                'category'     => 'Main Course',
                'difficulty'   => 'hard',
                'cooking_time' => 180,
                'servings'     => 6,
                'image'        => null,
                'is_premium'   => 1,
            'coin_price'   => 10,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 2,
                'title'        => 'Nasi Goreng Kampung',
                'slug'         => 'nasi-goreng-kampung',
                'description'  => 'Nasi goreng kampung sederhana namun penuh cita rasa. Menggunakan bumbu dasar yang mudah didapat dengan rasa yang nendang.',
                'cuisine'      => 'Indonesian',
                'category'     => 'Main Course',
                'difficulty'   => 'easy',
                'cooking_time' => 20,
                'servings'     => 2,
                'image'        => null,
                'is_premium'   => 0,
            'coin_price'   => 0,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 2,
                'title'        => 'Soto Ayam Lamongan',
                'slug'         => 'soto-ayam-lamongan',
                'description'  => 'Soto ayam khas Lamongan dengan kuah kuning gurih, suwiran ayam, dan koya yang nikmat. Hidangan pembuka yang menghangatkan.',
                'cuisine'      => 'Indonesian',
                'category'     => 'Soup',
                'difficulty'   => 'medium',
                'cooking_time' => 45,
                'servings'     => 4,
                'image'        => null,
                'is_premium'   => 0,
            'coin_price'   => 0,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 3,
                'title'        => 'Tonkotsu Ramen',
                'slug'         => 'tonkotsu-ramen',
                'description'  => 'Ramen tonkotsu dengan kaldu tulang babi yang dimasak selama berjam-jam hingga creamy. Dilengkapi chashu, ajitsuke tamago, dan nori.',
                'cuisine'      => 'Japanese',
                'category'     => 'Main Course',
                'difficulty'   => 'hard',
                'cooking_time' => 240,
                'servings'     => 4,
                'image'        => null,
                'is_premium'   => 1,
            'coin_price'   => 10,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 3,
                'title'        => 'Miso Soup',
                'slug'         => 'miso-soup',
                'description'  => 'Sup miso Jepang klasik dengan dashi, tahu sutra, dan wakame. Hangat dan menyehatkan, cocok sebagai pendamping makanan.',
                'cuisine'      => 'Japanese',
                'category'     => 'Soup',
                'difficulty'   => 'easy',
                'cooking_time' => 15,
                'servings'     => 2,
                'image'        => null,
                'is_premium'   => 0,
            'coin_price'   => 0,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 3,
                'title'        => 'Salmon Sashimi',
                'slug'         => 'salmon-sashimi',
                'description'  => 'Salmon sashimi segar dengan irisan sempurna. Disajikan dengan wasabi, pickled ginger, dan kecap asin berkualitas.',
                'cuisine'      => 'Japanese',
                'category'     => 'Appetizer',
                'difficulty'   => 'medium',
                'cooking_time' => 30,
                'servings'     => 2,
                'image'        => null,
                'is_premium'   => 1,
            'coin_price'   => 10,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 4,
                'title'        => 'Pasta Carbonara',
                'slug'         => 'pasta-carbonara',
                'description'  => 'Carbonara autentik ala Roma tanpa cream! Hanya menggunakan guanciale, pecorino romano, telur, dan lada hitam.',
                'cuisine'      => 'Italian',
                'category'     => 'Main Course',
                'difficulty'   => 'medium',
                'cooking_time' => 25,
                'servings'     => 2,
                'image'        => null,
                'is_premium'   => 0,
            'coin_price'   => 0,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 4,
                'title'        => 'Tiramisu',
                'slug'         => 'tiramisu',
                'description'  => 'Tiramisu klasik Italia dengan lapisan savoiardi yang direndam espresso, krim mascarpone lembut, dan taburan cocoa.',
                'cuisine'      => 'Italian',
                'category'     => 'Dessert',
                'difficulty'   => 'medium',
                'cooking_time' => 60,
                'servings'     => 6,
                'image'        => null,
                'is_premium'   => 1,
            'coin_price'   => 10,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 2,
                'title'        => 'Kimchi Jjigae',
                'slug'         => 'kimchi-jjigae',
                'description'  => 'Sup kimchi Korea yang hangat dan pedas dengan tahu, daging babi, dan kimchi fermentasi. Cocok untuk hari dingin.',
                'cuisine'      => 'Korean',
                'category'     => 'Main Course',
                'difficulty'   => 'medium',
                'cooking_time' => 40,
                'servings'     => 3,
                'image'        => null,
                'is_premium'   => 0,
            'coin_price'   => 0,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 3,
                'title'        => 'Pad Thai',
                'slug'         => 'pad-thai',
                'description'  => 'Pad Thai klasik Thailand dengan mie beras, udang, tahu, taoge, dan saus tamarind yang manis asam. Ditaburi kacang tanah sangrai.',
                'cuisine'      => 'Thai',
                'category'     => 'Main Course',
                'difficulty'   => 'easy',
                'cooking_time' => 30,
                'servings'     => 2,
                'image'        => null,
                'is_premium'   => 0,
            'coin_price'   => 0,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 4,
                'title'        => 'Risotto ai Funghi',
                'slug'         => 'risotto-ai-funghi',
                'description'  => 'Risotto jamur ala Italia dengan arborio rice yang creamy, campuran jamur porcini dan champignon, dan taburan parmesan.',
                'cuisine'      => 'Italian',
                'category'     => 'Main Course',
                'difficulty'   => 'hard',
                'cooking_time' => 45,
                'servings'     => 4,
                'image'        => null,
                'is_premium'   => 1,
            'coin_price'   => 10,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'chef_id'      => 2,
                'title'        => 'Gado-Gado Jakarta',
                'slug'         => 'gado-gado-jakarta',
                'description'  => 'Gado-gado khas Jakarta dengan bumbu kacang yang gurih dan creamy. Terdiri dari sayuran segar, tahu, tempe, dan lontong.',
                'cuisine'      => 'Indonesian',
                'category'     => 'Appetizer',
                'difficulty'   => 'easy',
                'cooking_time' => 25,
                'servings'     => 3,
                'image'        => null,
                'is_premium'   => 1,
            'coin_price'   => 10,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('recipes')->insertBatch($recipes);

        // ─── Ingredients ──────────────────────────────────────────────────
        $ingredients = [
            // 1. Rendang Padang Asli (recipe_id = 1)
            ['recipe_id' => 1, 'name' => 'Daging sapi (has dalam)', 'amount' => '1', 'unit' => 'kg', 'sort_order' => 1],
            ['recipe_id' => 1, 'name' => 'Santan kental', 'amount' => '800', 'unit' => 'ml', 'sort_order' => 2],
            ['recipe_id' => 1, 'name' => 'Cabai merah keriting', 'amount' => '15', 'unit' => 'buah', 'sort_order' => 3],
            ['recipe_id' => 1, 'name' => 'Serai', 'amount' => '3', 'unit' => 'batang', 'sort_order' => 4],
            ['recipe_id' => 1, 'name' => 'Lengkuas', 'amount' => '3', 'unit' => 'cm', 'sort_order' => 5],
            ['recipe_id' => 1, 'name' => 'Daun kunyit', 'amount' => '3', 'unit' => 'lembar', 'sort_order' => 6],

            // 2. Nasi Goreng Kampung (recipe_id = 2)
            ['recipe_id' => 2, 'name' => 'Nasi putih dingin', 'amount' => '400', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 2, 'name' => 'Telur ayam', 'amount' => '2', 'unit' => 'butir', 'sort_order' => 2],
            ['recipe_id' => 2, 'name' => 'Kecap manis', 'amount' => '3', 'unit' => 'sdm', 'sort_order' => 3],
            ['recipe_id' => 2, 'name' => 'Bawang merah', 'amount' => '5', 'unit' => 'siung', 'sort_order' => 4],
            ['recipe_id' => 2, 'name' => 'Cabai rawit', 'amount' => '5', 'unit' => 'buah', 'sort_order' => 5],

            // 3. Soto Ayam Lamongan (recipe_id = 3)
            ['recipe_id' => 3, 'name' => 'Ayam kampung', 'amount' => '500', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 3, 'name' => 'Kunyit', 'amount' => '3', 'unit' => 'cm', 'sort_order' => 2],
            ['recipe_id' => 3, 'name' => 'Jahe', 'amount' => '2', 'unit' => 'cm', 'sort_order' => 3],
            ['recipe_id' => 3, 'name' => 'Daun jeruk', 'amount' => '3', 'unit' => 'lembar', 'sort_order' => 4],
            ['recipe_id' => 3, 'name' => 'Koya', 'amount' => '50', 'unit' => 'gram', 'sort_order' => 5],

            // 4. Tonkotsu Ramen (recipe_id = 4)
            ['recipe_id' => 4, 'name' => 'Tulang babi', 'amount' => '2', 'unit' => 'kg', 'sort_order' => 1],
            ['recipe_id' => 4, 'name' => 'Mie ramen', 'amount' => '400', 'unit' => 'gram', 'sort_order' => 2],
            ['recipe_id' => 4, 'name' => 'Chashu (perut babi)', 'amount' => '500', 'unit' => 'gram', 'sort_order' => 3],
            ['recipe_id' => 4, 'name' => 'Telur (ajitsuke tamago)', 'amount' => '4', 'unit' => 'butir', 'sort_order' => 4],
            ['recipe_id' => 4, 'name' => 'Kecap asin Jepang', 'amount' => '100', 'unit' => 'ml', 'sort_order' => 5],
            ['recipe_id' => 4, 'name' => 'Nori', 'amount' => '4', 'unit' => 'lembar', 'sort_order' => 6],

            // 5. Miso Soup (recipe_id = 5)
            ['recipe_id' => 5, 'name' => 'Pasta miso putih', 'amount' => '3', 'unit' => 'sdm', 'sort_order' => 1],
            ['recipe_id' => 5, 'name' => 'Dashi granule', 'amount' => '1', 'unit' => 'sdm', 'sort_order' => 2],
            ['recipe_id' => 5, 'name' => 'Tahu sutra', 'amount' => '150', 'unit' => 'gram', 'sort_order' => 3],
            ['recipe_id' => 5, 'name' => 'Wakame (rumput laut kering)', 'amount' => '1', 'unit' => 'sdm', 'sort_order' => 4],
            ['recipe_id' => 5, 'name' => 'Daun bawang', 'amount' => '1', 'unit' => 'batang', 'sort_order' => 5],

            // 6. Salmon Sashimi (recipe_id = 6)
            ['recipe_id' => 6, 'name' => 'Salmon segar (sashimi grade)', 'amount' => '300', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 6, 'name' => 'Kecap asin Jepang', 'amount' => '50', 'unit' => 'ml', 'sort_order' => 2],
            ['recipe_id' => 6, 'name' => 'Wasabi', 'amount' => '10', 'unit' => 'gram', 'sort_order' => 3],
            ['recipe_id' => 6, 'name' => 'Pickled ginger (gari)', 'amount' => '30', 'unit' => 'gram', 'sort_order' => 4],

            // 7. Pasta Carbonara (recipe_id = 7)
            ['recipe_id' => 7, 'name' => 'Spaghetti', 'amount' => '200', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 7, 'name' => 'Guanciale', 'amount' => '150', 'unit' => 'gram', 'sort_order' => 2],
            ['recipe_id' => 7, 'name' => 'Pecorino Romano', 'amount' => '80', 'unit' => 'gram', 'sort_order' => 3],
            ['recipe_id' => 7, 'name' => 'Kuning telur', 'amount' => '4', 'unit' => 'butir', 'sort_order' => 4],
            ['recipe_id' => 7, 'name' => 'Lada hitam', 'amount' => '1', 'unit' => 'sdt', 'sort_order' => 5],

            // 8. Tiramisu (recipe_id = 8)
            ['recipe_id' => 8, 'name' => 'Mascarpone', 'amount' => '500', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 8, 'name' => 'Savoiardi (ladyfinger)', 'amount' => '300', 'unit' => 'gram', 'sort_order' => 2],
            ['recipe_id' => 8, 'name' => 'Espresso (dingin)', 'amount' => '300', 'unit' => 'ml', 'sort_order' => 3],
            ['recipe_id' => 8, 'name' => 'Telur', 'amount' => '4', 'unit' => 'butir', 'sort_order' => 4],
            ['recipe_id' => 8, 'name' => 'Gula pasir', 'amount' => '100', 'unit' => 'gram', 'sort_order' => 5],
            ['recipe_id' => 8, 'name' => 'Cocoa powder', 'amount' => '2', 'unit' => 'sdm', 'sort_order' => 6],

            // 9. Kimchi Jjigae (recipe_id = 9)
            ['recipe_id' => 9, 'name' => 'Kimchi fermentasi', 'amount' => '300', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 9, 'name' => 'Daging babi (perut)', 'amount' => '200', 'unit' => 'gram', 'sort_order' => 2],
            ['recipe_id' => 9, 'name' => 'Tahu sutra', 'amount' => '200', 'unit' => 'gram', 'sort_order' => 3],
            ['recipe_id' => 9, 'name' => 'Gochugaru (bubuk cabai Korea)', 'amount' => '2', 'unit' => 'sdm', 'sort_order' => 4],
            ['recipe_id' => 9, 'name' => 'Daun bawang', 'amount' => '2', 'unit' => 'batang', 'sort_order' => 5],

            // 10. Pad Thai (recipe_id = 10)
            ['recipe_id' => 10, 'name' => 'Mie beras pad thai', 'amount' => '200', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 10, 'name' => 'Udang', 'amount' => '150', 'unit' => 'gram', 'sort_order' => 2],
            ['recipe_id' => 10, 'name' => 'Saus tamarind', 'amount' => '3', 'unit' => 'sdm', 'sort_order' => 3],
            ['recipe_id' => 10, 'name' => 'Kacang tanah sangrai', 'amount' => '50', 'unit' => 'gram', 'sort_order' => 4],
            ['recipe_id' => 10, 'name' => 'Taoge', 'amount' => '100', 'unit' => 'gram', 'sort_order' => 5],

            // 11. Risotto ai Funghi (recipe_id = 11)
            ['recipe_id' => 11, 'name' => 'Beras arborio', 'amount' => '300', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 11, 'name' => 'Jamur porcini (kering)', 'amount' => '30', 'unit' => 'gram', 'sort_order' => 2],
            ['recipe_id' => 11, 'name' => 'Jamur champignon', 'amount' => '200', 'unit' => 'gram', 'sort_order' => 3],
            ['recipe_id' => 11, 'name' => 'Kaldu sayur', 'amount' => '1', 'unit' => 'liter', 'sort_order' => 4],
            ['recipe_id' => 11, 'name' => 'Parmesan', 'amount' => '80', 'unit' => 'gram', 'sort_order' => 5],
            ['recipe_id' => 11, 'name' => 'Mentega', 'amount' => '50', 'unit' => 'gram', 'sort_order' => 6],

            // 12. Gado-Gado Jakarta (recipe_id = 12)
            ['recipe_id' => 12, 'name' => 'Kacang tanah', 'amount' => '200', 'unit' => 'gram', 'sort_order' => 1],
            ['recipe_id' => 12, 'name' => 'Tahu goreng', 'amount' => '3', 'unit' => 'potong', 'sort_order' => 2],
            ['recipe_id' => 12, 'name' => 'Tempe goreng', 'amount' => '3', 'unit' => 'potong', 'sort_order' => 3],
            ['recipe_id' => 12, 'name' => 'Kangkung', 'amount' => '1', 'unit' => 'ikat', 'sort_order' => 4],
            ['recipe_id' => 12, 'name' => 'Lontong', 'amount' => '3', 'unit' => 'potong', 'sort_order' => 5],
            ['recipe_id' => 12, 'name' => 'Telur rebus', 'amount' => '3', 'unit' => 'butir', 'sort_order' => 6],
        ];

        $this->db->table('ingredients')->insertBatch($ingredients);

        // ─── Steps ────────────────────────────────────────────────────────
        $steps = [
            // 1. Rendang Padang Asli
            ['recipe_id' => 1, 'sort_order' => 1, 'description' => 'Potong daging sapi berukuran 3x3 cm. Haluskan bumbu: cabai merah, bawang merah, bawang putih, jahe, lengkuas, dan kunyit.', 'image' => null, 'tip' => 'Gunakan daging has dalam agar rendang lebih empuk.'],
            ['recipe_id' => 1, 'sort_order' => 2, 'description' => 'Tumis bumbu halus bersama serai, daun kunyit, dan daun jeruk hingga harum dan matang. Masak sekitar 15 menit.', 'image' => null, 'tip' => 'Pastikan bumbu benar-benar matang agar tidak langu.'],
            ['recipe_id' => 1, 'sort_order' => 3, 'description' => 'Masukkan potongan daging sapi, aduk rata dengan bumbu. Tuang santan kental sedikit demi sedikit sambil terus diaduk.', 'image' => null, 'tip' => 'Aduk terus agar santan tidak pecah.'],
            ['recipe_id' => 1, 'sort_order' => 4, 'description' => 'Masak dengan api kecil selama 3-4 jam sambil sesekali diaduk hingga kuah mengering dan daging berwarna cokelat gelap.', 'image' => null, 'tip' => 'Sabar adalah kunci rendang yang enak. Jangan buru-buru memperbesar api.'],
            ['recipe_id' => 1, 'sort_order' => 5, 'description' => 'Rendang siap disajikan. Rendang bisa disimpan di lemari es hingga 1 minggu dan akan lebih enak saat dipanaskan kembali.', 'image' => null, 'tip' => 'Rendang makin lama makin enak karena bumbu semakin meresap.'],

            // 2. Nasi Goreng Kampung
            ['recipe_id' => 2, 'sort_order' => 1, 'description' => 'Haluskan bawang merah, bawang putih, dan cabai rawit. Gunakan nasi yang sudah dingin agar nasi goreng tidak lembek.', 'image' => null, 'tip' => 'Nasi yang sudah disimpan semalam di kulkas menghasilkan nasi goreng terbaik.'],
            ['recipe_id' => 2, 'sort_order' => 2, 'description' => 'Panaskan minyak dengan api besar. Tumis bumbu halus hingga harum, lalu orak-arik telur di sisi wajan.', 'image' => null, 'tip' => 'Gunakan api besar agar nasi goreng berasap (wok hei).'],
            ['recipe_id' => 2, 'sort_order' => 3, 'description' => 'Masukkan nasi dingin, kecap manis, garam, dan merica. Aduk rata dengan gerakan membalik hingga nasi terbalut bumbu merata.', 'image' => null, 'tip' => null],
            ['recipe_id' => 2, 'sort_order' => 4, 'description' => 'Sajikan dengan pelengkap kerupuk, acar timun, dan telur mata sapi di atasnya.', 'image' => null, 'tip' => 'Taburi bawang goreng untuk aroma yang lebih sedap.'],

            // 3. Soto Ayam Lamongan
            ['recipe_id' => 3, 'sort_order' => 1, 'description' => 'Rebus ayam kampung dengan air secukupnya. Buang busa yang mengapung. Haluskan bumbu: kunyit, jahe, bawang merah, bawang putih.', 'image' => null, 'tip' => 'Gunakan ayam kampung untuk kuah yang lebih gurih.'],
            ['recipe_id' => 3, 'sort_order' => 2, 'description' => 'Tumis bumbu halus bersama serai dan daun jeruk hingga harum. Masukkan ke dalam rebusan ayam.', 'image' => null, 'tip' => null],
            ['recipe_id' => 3, 'sort_order' => 3, 'description' => 'Masak hingga ayam empuk sekitar 30 menit. Angkat ayam, suwir-suwir dagingnya.', 'image' => null, 'tip' => null],
            ['recipe_id' => 3, 'sort_order' => 4, 'description' => 'Siapkan mangkuk dengan nasi, suwiran ayam, tauge, seledri, dan daun bawang. Tuang kuah soto panas. Taburi koya dan bawang goreng.', 'image' => null, 'tip' => 'Koya adalah kunci rasa soto Lamongan. Bisa dibuat dari kerupuk udang yang dihaluskan.'],

            // 4. Tonkotsu Ramen
            ['recipe_id' => 4, 'sort_order' => 1, 'description' => 'Blanching tulang babi dalam air mendidih selama 10 menit. Buang air, cuci bersih tulang. Ini penting untuk menghilangkan bau amis.', 'image' => null, 'tip' => 'Jangan skip langkah blanching ini, kaldu akan lebih bersih.'],
            ['recipe_id' => 4, 'sort_order' => 2, 'description' => 'Rebus tulang babi dengan air baru dalam api besar selama 8-12 jam. Tambahkan bawang putih, jahe, dan daun bawang. Aduk sesekali.', 'image' => null, 'tip' => 'Api besar membuat kaldu menjadi putih susu dan creamy.'],
            ['recipe_id' => 4, 'sort_order' => 3, 'description' => 'Buat chashu: gulung perut babi, ikat dengan tali. Rebus dengan kecap asin, mirin, sake, gula, dan bawang putih selama 2 jam.', 'image' => null, 'tip' => 'Simpan chashu dalam cairan rebusan semalaman agar bumbu meresap.'],
            ['recipe_id' => 4, 'sort_order' => 4, 'description' => 'Buat ajitsuke tamago: rebus telur 6 menit 30 detik, lalu rendam dalam campuran kecap dan mirin selama 4 jam.', 'image' => null, 'tip' => 'Waktu rebus harus tepat untuk mendapatkan kuning telur yang creamy.'],
            ['recipe_id' => 4, 'sort_order' => 5, 'description' => 'Rebus mie ramen sesuai petunjuk kemasan. Siapkan mangkuk dengan tare (bumbu dasar), tuang kaldu panas. Tambahkan mie, chashu, telur, nori, dan daun bawang.', 'image' => null, 'tip' => 'Sajikan secepatnya setelah mie dimasukkan agar mie tidak lembek.'],

            // 5. Miso Soup
            ['recipe_id' => 5, 'sort_order' => 1, 'description' => 'Didihkan 600ml air, lalu larutkan dashi granule. Matikan api sesaat.', 'image' => null, 'tip' => null],
            ['recipe_id' => 5, 'sort_order' => 2, 'description' => 'Potong tahu sutra dadu kecil. Rendam wakame dalam air selama 5 menit hingga mengembang.', 'image' => null, 'tip' => null],
            ['recipe_id' => 5, 'sort_order' => 3, 'description' => 'Masukkan tahu dan wakame ke dalam kaldu. Didihkan kembali dengan api kecil.', 'image' => null, 'tip' => 'Jangan mendidihkan terlalu lama agar tahu tidak hancur.'],
            ['recipe_id' => 5, 'sort_order' => 4, 'description' => 'Matikan api. Larutkan miso paste dengan sedikit kaldu, lalu tuang kembali ke dalam panci. Aduk rata. Taburi daun bawang iris.', 'image' => null, 'tip' => 'Jangan mendidihkan sup setelah miso ditambahkan agar nutrisi dan rasanya terjaga.'],

            // 6. Salmon Sashimi
            ['recipe_id' => 6, 'sort_order' => 1, 'description' => 'Pastikan salmon sashimi grade dalam kondisi dingin. Simpan di kulkas hingga saat disajikan.', 'image' => null, 'tip' => 'Hanya gunakan salmon sashimi grade yang aman dimakan mentah.'],
            ['recipe_id' => 6, 'sort_order' => 2, 'description' => 'Iris salmon dengan gerakan memotong menarik ke arah tubuh Anda. Potongan harus tebal sekitar 5-7mm.', 'image' => null, 'tip' => 'Gunakan pisau sashimi (yanagiba) untuk hasil terbaik. Pisau harus sangat tajam.'],
            ['recipe_id' => 6, 'sort_order' => 3, 'description' => 'Tata irisan salmon di atas piring. Sajikan dengan wasabi, pickled ginger, dan kecap asin di mangkuk terpisah.', 'image' => null, 'tip' => 'Letakkan wasabi langsung di atas ikan, jangan larutkan dalam kecap asing.'],

            // 7. Pasta Carbonara
            ['recipe_id' => 7, 'sort_order' => 1, 'description' => 'Rebus spaghetti dalam air bergaram hingga al dente. Sisakan 1 cangkir air rebusan pasta.', 'image' => null, 'tip' => 'Jangan tambah minyak ke air rebusan.'],
            ['recipe_id' => 7, 'sort_order' => 2, 'description' => 'Potong guanciale menjadi strip. Masak di wajan tanpa minyak dengan api sedang hingga renyah dan lemaknya keluar.', 'image' => null, 'tip' => 'Guanciale memberikan rasa paling autentik. Bisa diganti pancetta.'],
            ['recipe_id' => 7, 'sort_order' => 3, 'description' => 'Campurkan kuning telur dengan pecorino romano parut dan lada hitam yang baru ditumbuk. Aduk hingga menjadi pasta kental.', 'image' => null, 'tip' => null],
            ['recipe_id' => 7, 'sort_order' => 4, 'description' => 'Matikan api wajan. Masukkan spaghetti ke wajan guanciale, lalu tuang campuran telur-keju sambil diaduk cepat. Tambahkan air pasta jika perlu.', 'image' => null, 'tip' => 'WAJIB matikan api! Panas residual sudah cukup mengolah telur tanpa membuatnya orak-arik.'],

            // 8. Tiramisu
            ['recipe_id' => 8, 'sort_order' => 1, 'description' => 'Pisahkan kuning dan putih telur. Kocok kuning telur dengan gula hingga pucat dan mengembang. Tambahkan mascarpone, aduk rata.', 'image' => null, 'tip' => 'Mascarpone harus suhu ruang agar mudah dicampur.'],
            ['recipe_id' => 8, 'sort_order' => 2, 'description' => 'Kocok putih telur hingga kaku (stiff peak). Lipat ke dalam campuran mascarpone perlahan dengan gerakan melipat.', 'image' => null, 'tip' => 'Lipat perlahan agar adonan tetap ringan dan berudara.'],
            ['recipe_id' => 8, 'sort_order' => 3, 'description' => 'Celupkan savoiardi ke espresso dingin selama 2-3 detik per sisi. Jangan terlalu lama agar tidak lembek.', 'image' => null, 'tip' => 'Penyelupan harus cepat! Savoiardi yang terlalu basah membuat tiramisu berair.'],
            ['recipe_id' => 8, 'sort_order' => 4, 'description' => 'Tata savoiardi di dasar wajan, tutup dengan krim mascarpone. Ulangi lapisan. Taburi cocoa powder di atasnya. Simpan di kulkas minimal 4 jam.', 'image' => null, 'tip' => 'Tiramisu paling enak jika didiamkan semalaman di kulkas.'],

            // 9. Kimchi Jjigae
            ['recipe_id' => 9, 'sort_order' => 1, 'description' => 'Potong daging babi perut menjadi potongan kecil. Potong kimchi menjadi ukuran satu gigitan, sisakan air kimchi.', 'image' => null, 'tip' => 'Kimchi yang sudah fermentasi lebih lama (asam) menghasilkan jjigae yang lebih enak.'],
            ['recipe_id' => 9, 'sort_order' => 2, 'description' => 'Tumis daging babi dalam panci hingga keluar lemaknya. Tambahkan kimchi dan air kimchi, masak 5 menit.', 'image' => null, 'tip' => null],
            ['recipe_id' => 9, 'sort_order' => 3, 'description' => 'Tambahkan air, gochugaru, dan bawang putih cincang. Didihkan lalu kecilkan api, masak 20 menit.', 'image' => null, 'tip' => null],
            ['recipe_id' => 9, 'sort_order' => 4, 'description' => 'Masukkan tahu sutra yang dipotong dadu dan daun bawang. Masak 5 menit lagi. Sajikan panas-panas dengan nasi putih.', 'image' => null, 'tip' => 'Sajikan langsung dari panci di atas kompor portabel untuk pengalaman Korea autentik.'],

            // 10. Pad Thai
            ['recipe_id' => 10, 'sort_order' => 1, 'description' => 'Rendam mie beras dalam air hangat selama 20 menit hingga lunak. Tiriskan. Campurkan saus: saus tamarind, gula aren, kecap ikan, dan air.', 'image' => null, 'tip' => 'Jangan rendam mie terlalu lama agar tidak lembek saat ditumis.'],
            ['recipe_id' => 10, 'sort_order' => 2, 'description' => 'Panaskan wajan dengan api besar. Tumis bawang putih hingga harum, masukkan udang, masak hingga berubah warna.', 'image' => null, 'tip' => null],
            ['recipe_id' => 10, 'sort_order' => 3, 'description' => 'Dorong udang ke sisi wajan. Orak-arik telur di tengah wajan. Masukkan mie dan saus tamarind, aduk rata.', 'image' => null, 'tip' => null],
            ['recipe_id' => 10, 'sort_order' => 4, 'description' => 'Masukkan taoge dan daun bawang, aduk sebentar. Angkat dari api. Sajikan dengan kacang tanah sangrai, irisan jeruk limau, dan cabai rawit.', 'image' => null, 'tip' => 'Perasan jeruk limau sebelum dimakan membuat pad thai lebih segar.'],

            // 11. Risotto ai Funghi
            ['recipe_id' => 11, 'sort_order' => 1, 'description' => 'Rendam jamur porcini kering dalam air hangat 30 menit. Saring dan simpan air rendaman. Panaskan kaldu sayur tetapi jangan didihkan.', 'image' => null, 'tip' => 'Air rendaman porcini adalah emas cair — penuh rasa umami.'],
            ['recipe_id' => 11, 'sort_order' => 2, 'description' => 'Tumis bawang bombay cincang halus dengan mentega hingga transparan. Masukkan beras arborio, aduk 2 menit hingga butiran berlapis minyak (tosatura).', 'image' => null, 'tip' => 'Tahap tosatura penting untuk membuka pori-pori beras agar menyerap kaldu.'],
            ['recipe_id' => 11, 'sort_order' => 3, 'description' => 'Tambahkan jamur champignon iris dan porcini yang sudah direndam. Aduk rata. Tuang anggur putih, aduk hingga terserap.', 'image' => null, 'tip' => null],
            ['recipe_id' => 11, 'sort_order' => 4, 'description' => 'Tuang kaldu satu sendok sayur pada satu waktu, aduk terus. Tambahkan kaldu berikutnya hanya setelah kaldu sebelumnya terserap. Proses ini memakan waktu 18-20 menit.', 'image' => null, 'tip' => 'Aduk terus dengan gerakan satu arah untuk melepas pati yang membuat risotto creamy.'],
            ['recipe_id' => 11, 'sort_order' => 5, 'description' => 'Matikan api. Masukkan mentega dingin dan parmesan (mantecatura). Aduk kuat hingga creamy. Sajikan segera di piring hangat.', 'image' => null, 'tip' => 'Mantecatura adalah rahasia risotto yang creamy sempurna. Jangan skip langkah ini!'],

            // 12. Gado-Gado Jakarta
            ['recipe_id' => 12, 'sort_order' => 1, 'description' => 'Rebus sayuran: kangkung, kacang panjang, kol, dan tauge. Tiriskan dan biarkan dingin. Goreng tahu dan tempe hingga kecokelatan.', 'image' => null, 'tip' => 'Jangan merebus sayuran terlalu lama agar tetap renyah.'],
            ['recipe_id' => 12, 'sort_order' => 2, 'description' => 'Buat bumbu kacang: sangrai kacang tanah, lalu haluskan bersama cabai, bawang putih, gula merah, garam, dan air asam. Tambahkan air secukupnya hingga konsistensi sedang.', 'image' => null, 'tip' => 'Bumbu kacang harus tidak terlalu kental dan tidak terlalu encer, seperti saus kental.'],
            ['recipe_id' => 12, 'sort_order' => 3, 'description' => 'Potong lontong. Tata di piring: lontong, sayuran, tahu goreng, tempe goreng, dan telur rebus belah dua.', 'image' => null, 'tip' => null],
            ['recipe_id' => 12, 'sort_order' => 4, 'description' => 'Siram bumbu kacang merata di atas semua bahan. Sajikan dengan kerupuk emping dan bawang goreng.', 'image' => null, 'tip' => 'Kerupuk emping adalah pelengkap wajib gado-gado Jakarta yang autentik.'],
        ];

        $this->db->table('steps')->insertBatch($steps);

        // ─── Subscription for Sari (user_id = 6, plan_id = 2 Premium Monthly) ───
        $subscriptions = [
            [
                'user_id'    => 6,
                'plan_id'    => 2,
                'status'     => 'active',
                'start_date' => $now,
                'end_date'   => date('Y-m-d H:i:s', strtotime('+30 days')),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('subscriptions')->insertBatch($subscriptions);

        // ─── Bookmarks ────────────────────────────────────────────────────
        $bookmarks = [
            // Andi (user_id=5) bookmarks free recipes
            ['user_id' => 5, 'recipe_id' => 2,  'created_at' => $now], // Nasi Goreng Kampung
            ['user_id' => 5, 'recipe_id' => 5,  'created_at' => $now], // Miso Soup
            ['user_id' => 5, 'recipe_id' => 7,  'created_at' => $now], // Pasta Carbonara
            ['user_id' => 5, 'recipe_id' => 9,  'created_at' => $now], // Kimchi Jjigae
            // Sari (user_id=6) bookmarks various recipes (including premium)
            ['user_id' => 6, 'recipe_id' => 1,  'created_at' => $now], // Rendang Padang Asli
            ['user_id' => 6, 'recipe_id' => 3,  'created_at' => $now], // Soto Ayam Lamongan
            ['user_id' => 6, 'recipe_id' => 4,  'created_at' => $now], // Tonkotsu Ramen
            ['user_id' => 6, 'recipe_id' => 8,  'created_at' => $now], // Tiramisu
            ['user_id' => 6, 'recipe_id' => 10, 'created_at' => $now], // Pad Thai
            ['user_id' => 6, 'recipe_id' => 12, 'created_at' => $now], // Gado-Gado Jakarta
        ];

        $this->db->table('bookmarks')->insertBatch($bookmarks);
    }
}
