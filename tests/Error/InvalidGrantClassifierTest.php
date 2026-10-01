<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Error;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\ResponseExceptionInterface;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidGrantClassifier::class)]
final class InvalidGrantClassifierTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testIsInvalidGrantIsFalseForNonResponseExceptions(): void
    {
        self::assertFalse((new InvalidGrantClassifier())->isInvalidGrant(self::createStub(ExceptionInterface::class)));
    }

    /**
     * @param null|array<array-key, mixed> $decodedBody
     *
     * @throws Exception
     */
    #[TestWith([['error' => 'invalid_grant'], true])]
    #[TestWith([['error' => 'invalid_client'], false])]
    #[TestWith([['foo' => 'bar'], false])]
    #[TestWith([null, false])]
    public function testIsInvalidGrantReadsDecodedBody(?array $decodedBody, bool $expected): void
    {
        $exception = self::createStub(ResponseExceptionInterface::class);
        $exception->method('getDecodedBody')->willReturn($decodedBody);

        self::assertSame($expected, (new InvalidGrantClassifier())->isInvalidGrant($exception));
    }
}
