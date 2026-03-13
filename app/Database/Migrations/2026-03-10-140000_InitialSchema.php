<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitialSchema extends Migration
{
    public function up()
    {
        // Drop legacy table if exists
        $this->db->query("DROP TABLE IF EXISTS `stock_purchase` ");

        $queries = [
            "CREATE TABLE `categories` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `doctor_payments` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `doctor_id` int unsigned NOT NULL,
              `amount` decimal(10,2) NOT NULL,
              `payment_date` date NOT NULL,
              `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Cash',
              `notes` text COLLATE utf8mb4_general_ci,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `doctor_payments_doctor_id_foreign` (`doctor_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `doctors` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
              `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
              `address` text COLLATE utf8mb4_general_ci,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `expenses` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
              `amount` decimal(10,2) NOT NULL,
              `expense_date` date NOT NULL,
              `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `notes` text COLLATE utf8mb4_general_ci,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `product_details` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `product_id` int unsigned NOT NULL,
              `cost` decimal(10,2) NOT NULL,
              `unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
              `unit_value` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
              `form_6` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `form_7` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `product_details_product_id_foreign` (`product_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `products` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
              `category_id` int unsigned NOT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `products_category_id_foreign` (`category_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `purchase_details` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `purchase_id` int unsigned NOT NULL,
              `product_id` int unsigned NOT NULL,
              `product_detail_id` int unsigned DEFAULT NULL,
              `batch_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
              `qty` int NOT NULL,
              `cost` decimal(10,2) NOT NULL,
              `price` decimal(10,2) NOT NULL,
              `mfg_date` date DEFAULT NULL,
              `exp_date` date DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `purchase_details_purchase_id_foreign` (`purchase_id`),
              KEY `purchase_details_product_id_foreign` (`product_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `purchases` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `vendor_id` int unsigned DEFAULT NULL,
              `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
              `note` text COLLATE utf8mb4_general_ci,
              `date` date NOT NULL,
              `status` enum('ordered','received','partial_paid','paid') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ordered',
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `purchases_vendor_id_foreign` (`vendor_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `sale_details` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `sale_id` int unsigned NOT NULL,
              `product_id` int unsigned NOT NULL,
              `product_detail_id` int unsigned NOT NULL,
              `stock_id` int unsigned NOT NULL COMMENT 'Refers to purchase_details.id',
              `qty` int NOT NULL,
              `sale_price` decimal(15,2) NOT NULL,
              `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
              `strength` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `sales` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `invoice_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `doctor_id` int unsigned DEFAULT NULL,
              `gross_amount` decimal(15,2) DEFAULT '0.00',
              `total_amount` decimal(15,2) DEFAULT '0.00',
              `total_discount` decimal(15,2) DEFAULT '0.00',
              `manual_dr_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `manual_dr_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `sale_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `sales_doctor_id_fk` (`doctor_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `settings` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
              `setting_value` text COLLATE utf8mb4_general_ci NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `setting_key` (`setting_key`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `users` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
              `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
              `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `username` (`username`),
              UNIQUE KEY `email` (`email`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `vendor_payments` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `vendor_id` int unsigned NOT NULL,
              `purchase_id` int unsigned DEFAULT NULL,
              `amount` decimal(10,2) NOT NULL,
              `payment_date` date NOT NULL,
              `notes` text COLLATE utf8mb4_general_ci,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `vendor_payments_vendor_id_foreign` (`vendor_id`),
              KEY `vp_purchase_id_foreign` (`purchase_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `vendors` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
              `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
              `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
              `address` text COLLATE utf8mb4_general_ci,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        ];

        foreach ($queries as $q) {
            $this->db->query($q);
        }

        // Default settings
        $this->db->table('settings')->insertBatch([
            ['setting_key' => 'pharmacy_name', 'setting_value' => 'Galaxy Pharmacy'],
            ['setting_key' => 'pharmacy_phone', 'setting_value' => '+92 300 0000000'],
            ['setting_key' => 'currency_symbol', 'setting_value' => 'Rs.'],
        ]);
    }

    public function down()
    {
        $tables = [
            'vendor_payments', 'vendors', 'users', 'settings', 'stock_purchase',
            'sales', 'sale_details', 'purchases', 'purchase_details', 'products',
            'product_details', 'expenses', 'doctors', 'doctor_payments', 'categories'
        ];
        
        foreach ($tables as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
