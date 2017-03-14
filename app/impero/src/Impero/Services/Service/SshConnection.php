<?php namespace Impero\Services\Service;

use Exception;

class SshConnection
{

    /**
     * @var resource
     */
    protected $connection;

    public function __construct()
    {
        $this->connection = ssh2_connect('zero.gonparty.eu', 22222);

        $auth = ssh2_auth_pubkey_file(
            $this->connection,
            'impero',
            path('storage') . 'private/keys/id_rsa_impero.pub',
            path('storage') . 'private/keys/id_rsa_impero',
            ''
        );

        if (!$auth) {
            throw new Exception("Cannot authenticate with key");
        }
    }

    public function exec($command, &$errorStreamContent = null)
    {
        $stream = ssh2_exec($this->connection, $command);

        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $errorStreamContent = stream_get_contents($errorStream);

        return stream_get_contents($stream);
    }

}