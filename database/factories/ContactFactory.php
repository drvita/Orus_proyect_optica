<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('es_MX');

        // Determinar si es empresa o persona física
        $isBusiness = $faker->boolean(30); // 30% probabilidad de ser empresa

        if ($isBusiness) {
            $name = $faker->company();
            $rfc = strtoupper(
                substr(preg_replace('/[AEIOU\s]/', '', $name), 0, 3) . // 3 consonantes del nombre
                    $faker->date('y/m/d') .
                    $faker->bothify('???') // 3 caracteres alfanuméricos
            );
        } else {
            $name = $faker->name();
            $apellidoPaterno = explode(' ', $name)[1] ?? 'XXX';
            $apellidoMaterno = explode(' ', $name)[2] ?? 'XXX';
            $primerNombre = explode(' ', $name)[0] ?? 'XXX';

            $rfc = strtoupper(
                substr($apellidoPaterno, 0, 2) .
                    substr($apellidoMaterno, 0, 1) .
                    substr($primerNombre, 0, 1) .
                    $faker->date('ymd') .
                    $faker->bothify('???') // 3 caracteres alfanuméricos
            );
        }

        // Generar de 1 a 3 números telefónicos
        $phones = [];
        $numPhones = $faker->numberBetween(1, 3);
        for ($i = 0; $i < $numPhones; $i++) {
            $phones[] = $faker->numerify('##########'); // 10 dígitos
        }

        // Generar dirección
        $address = [
            'calle' => $faker->streetName(),
            'numero_ext' => $faker->buildingNumber(),
            'numero_int' => $faker->optional(0.3)->bothify('###'),  // 30% probabilidad de tener número interior
            'colonia' => $faker->cityPrefix() . ' ' . $faker->lastName(),
            'cp' => $faker->postcode(),
            'ciudad' => $faker->city(),
            'estado' => $faker->state()
        ];

        // Generar email basado en el nombre
        $emailName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $email = substr($emailName, 0, 20) . '@' . $faker->safeEmailDomain();
        // $user = \App\Models\User::factory();

        return [
            'name' => $name,
            'rfc' => $rfc,
            'email' => $email,
            'telnumbers' => $phones,
            'birthday' => $isBusiness ? null : $faker->dateTimeBetween('-70 years', '-18 years'),
            'domicilio' => $address,
            'type' => $faker->boolean(70) ? 0 : 1, // 70% probabilidad de ser cliente
            'business' => $isBusiness,
            'user_id' => 2,
            'updated_id' => 2
        ];
    }
}
