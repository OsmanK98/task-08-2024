<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\BankAccount\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testItCanCreateMoney(): void
    {
        $money = new Money(100);

        $this->assertEquals(100, $money->toInt());
    }

    public function testItCanAddMoney(): void
    {
        $money = new Money(100);
        $moneyToAdd = new Money(50);

        $moneyAfterOperation = $money->add($moneyToAdd);

        $this->assertEquals(150, $moneyAfterOperation->toInt());
    }

    public function testItCanSubtractMoney(): void
    {
        $money = new Money(100);
        $moneyToSubtract = new Money(50);

        $moneyAfterOperation = $money->subtract($moneyToSubtract);

        $this->assertEquals(50, $moneyAfterOperation->toInt());
    }

    #[DataProvider('dataProviderCalculateAmountByPercentage')]
    public function testCalculateAmountByPercentage(int $initialAmount, float $percentage, int $expectedAmount): void
    {
        $money = new Money($initialAmount);
        $calculatedAmount = $money->calculateAmountByPercentage($percentage);

        $this->assertEquals($expectedAmount, $calculatedAmount->toInt());
    }

    public static function dataProviderCalculateAmountByPercentage(): array
    {
        return [
            [1000, 10, 100], // 10% of 1000 is 100
            [1000, 0, 0],    // 0% of 1000 is 0
            [1000, 100, 1000], // 100% of 1000 is 1000
            [1000, 50, 500], // 50% of 1000 is 500
            [1000, 25, 250], // 25% of 1000 is 250
            [5000, 0.5, 25], // 0.5% of 5000 is 25
            [100, 0.5, 0], // 0.5% of 100 is 0
        ];
    }
}
