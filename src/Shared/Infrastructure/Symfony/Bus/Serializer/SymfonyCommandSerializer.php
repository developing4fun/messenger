<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Serializer;


use Library\User\Application\CreateExternalUser\CreateExternalUserCommand;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use function json_decode;
use function json_encode;
use function serialize;

final class SymfonyCommandSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);
        $stamps = $this->decodeStamps($encodedEnvelope);

        $command = new CreateExternalUserCommand(
            $data['data']['id'],
            $data['data']['name']
        );

        return new Envelope($command, $stamps);
    }

    public function encode(Envelope $envelope): array
    {
        /** @var CreateExternalUserCommand $command */
        $command = $envelope->getMessage();
        $stamps = $this->extractStamps($envelope);

        return [
            'body' => json_encode(
                [
                    'data' => [
                        'id' => $command->userId(),
                        'name' => $command->userName(),
                    ]
                ]
            ),
            'headers' => [
                'stamps' => json_encode(serialize($stamps)),
            ]
        ];
    }

    private function decodeStamps(array $encodedEnvelope): array
    {
        $headers = $encodedEnvelope['headers'];

        if (isset($headers['stamps'])) {
            return unserialize(json_decode($headers['stamps']));
        }

        return [];
    }

    private function extractStamps(Envelope $envelope): array
    {
        $allStamps = [];

        foreach ($envelope->all() as $stamps) {
            $stamp = is_array($stamps) ? array_pop($stamps) : null;
            if (!empty($stamp) && !$stamp instanceof NonSendableStampInterface) {
                $allStamps[get_class($stamp)] = $stamp;
            }
        }

        return $allStamps;
    }
}
