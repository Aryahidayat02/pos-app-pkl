<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_displays_daily_line_and_transaction_candlestick_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $saleDate = now()->toDateString();
        $this->createSale($admin, 'INV-CHART-001', $saleDate.' 09:00:00', 11000);
        $this->createSale($admin, 'INV-CHART-002', $saleDate.' 10:00:00', 9000);
        $this->createSale($admin, 'INV-CHART-003', $saleDate.' 11:00:00', 15000);

        $response = $this->actingAs($admin)->get(route('reports.index', [
            'start_date' => $saleDate,
            'end_date' => $saleDate,
        ]));

        $response->assertOk()
            ->assertSee('sales-line-chart', false)
            ->assertSee('sales-candlestick-chart', false)
            ->assertViewHas('chartData', function (array $chartData) use ($saleDate): bool {
                return $chartData['line'] === [
                    ['date' => $saleDate, 'value' => 35000.0],
                ] && $chartData['candles'] === [
                    [
                        'date' => $saleDate,
                        'open' => 11000.0,
                        'high' => 15000.0,
                        'low' => 9000.0,
                        'close' => 15000.0,
                    ],
                ];
            });
    }

    public function test_report_rejects_an_end_date_before_the_start_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('reports.index', [
            'start_date' => '2026-11-20',
            'end_date' => '2026-11-19',
        ]));

        $response->assertSessionHasErrors('end_date');
    }

    private function createSale(User $user, string $invoiceNumber, string $saleDate, int $total): Sale
    {
        return Sale::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $user->id,
            'sale_date' => $saleDate,
            'subtotal' => $total,
            'discount' => 0,
            'total' => $total,
            'payment_method' => 'tunai',
            'paid_amount' => $total,
            'change_amount' => 0,
            'status' => 'paid',
        ]);
    }
}
