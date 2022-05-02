<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Serializer;

use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Infrastructure\Symfony\Bus\SymfonyIdStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use function array_map;
use function array_merge;
use function explode;
use function implode;
use function json_decode;
use function preg_replace;
use function serialize;
use function str_replace;
use function strtolower;
use function ucwords;
use function unserialize;
use const JSON_THROW_ON_ERROR;

final class SymfonyMessageSerializer implements SerializerInterface
{
    private const CLASS_SEPARATOR = '.';

    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter()),
            ],
            [
                new JsonEncoder(),
            ]
        );
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $headers = $encodedEnvelope['headers'];
        $body    = json_decode($encodedEnvelope['body'], true, 512, JSON_THROW_ON_ERROR);
        $name    = $this->decodeMessageName($body['metadata']['name']);

        $stamps = [];

        if (isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        $payload = $body['payload'];

        /** @var DomainEvent $message */
        $message = $name::fromPrimitives(
            $payload['aggregateId'],
            $payload['body'],
            $payload['eventId'],
            $payload['occurredOn']
        );

        return new Envelope($message, $stamps);
    }

    public function encode(Envelope $envelope): array
    {
        /** @var DomainEvent $message */
        $message = $envelope->getMessage();
        $allStamps = [];

        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }

        /** @var ?SymfonyIdStamp $id */
        $id = $envelope->last(SymfonyIdStamp::class);

        $data = $this->serializer->serialize(
            [
                'payload'  => $message->toPrimitives(),
                'metadata' => [
                    'id'   => $id?->value(),
                    'name' => $this->encodeMessageName(
                        $message::class
                    ),
                ],
            ],
            JsonEncoder::FORMAT
        );

        return [
            'body'    => $data,
            'headers' => [
                'stamps' => serialize($allStamps),
            ],
        ];
    }

    private function decodeMessageName(string $message): string
    {
        $text = array_map(static function (string $part) {
            return ucwords($part, '_');
        }, explode(self::CLASS_SEPARATOR, $message));

        return str_replace('_', '', implode('\\', $text));
    }

    private function encodeMessageName(string $message): string
    {
        return strtolower(
            preg_replace(
                '/([a-z])([A-Z])/',
                '$1_$2',
                str_replace('\\', self::CLASS_SEPARATOR, $message)
            )
        );
    }
}
