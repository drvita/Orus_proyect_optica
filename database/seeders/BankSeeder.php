<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            'Banamex',
            'Santander',
            'Scotiabank',
            'Banco Azteca',
            'HSBC',
            'BBVA',
            'Banorte',
            'Inbursa',
            'Banco del Bajío',
            'Afirme',
            'BanCoppel',
            'Banregio',
            'Invex',
            'Banjercito',
            'Multiva',
            'Mifel',
            'Monex',
            'Intercam',
            'Bansi',
            'CIBanco',
            'American Express',
            'Bank of America',
        ];

        // Specific aliases to merge existing value into the new correct format
        $aliases = [
            'Banco Azteca' => ['azteca'],
            'Banco del Bajío' => ['del bajio'],
        ];

        // Fetch all existing bank configs to check for duplicates
        $existingBanks = Config::where('name', 'bank')->get();

        foreach ($banks as $bankName) {
            $existing = $existingBanks->first(function ($config) use ($bankName, $aliases) {
                $val = $config->value;

                if (is_array($val) || is_object($val)) {
                    return false;
                }

                $normalizedVal = $this->normalize((string)$val);
                $normalizedBankName = $this->normalize($bankName);

                // Direct match
                if ($normalizedVal === $normalizedBankName) {
                    return true;
                }

                // Alias match
                if (isset($aliases[$bankName])) {
                    foreach ($aliases[$bankName] as $alias) {
                        if ($normalizedVal === $this->normalize($alias)) {
                            return true;
                        }
                    }
                }

                return false;
            });

            if ($existing) {
                if ($existing->value !== $bankName) {
                    $existing->update(['value' => $bankName]);
                }
            } else {
                Config::create([
                    'name' => 'bank',
                    'value' => $bankName
                ]);
            }
        }

        $branches = [
            [
                'name' => 'Tecnologico',
                'address' => 'Av. Tecnologico 32-A',
                'phone' => '3123125353'
            ],
            [
                'name' => 'Constitucion',
                'address' => 'Av. Constitución 32-A',
                'phone' => '3123125353'
            ]
        ];

        // Fetch all branches once
        $existingBranches = Config::where('name', 'branches')->get();

        foreach ($branches as $branch) {
            $existing = $existingBranches->first(function ($config) use ($branch) {
                $val = $config->value;

                if (is_string($val)) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $val = $decoded;
                    }
                }

                if (!is_array($val)) {
                    return false;
                }

                return isset($val['name']) && $this->normalize($val['name']) === $this->normalize($branch['name']);
            });

            if ($existing) {
                $existing->update(['value' => json_encode($branch)]);
            } else {
                Config::create([
                    'name' => 'branches',
                    'value' => json_encode($branch)
                ]);
            }
        }
    }

    /**
     * Normalizes a string by removing accents and converting to lowercase.
     *
     * @param string $string
     * @return string
     */
    private function normalize($string)
    {
        return Str::ascii(mb_strtolower($string, 'UTF-8'));
    }
}
