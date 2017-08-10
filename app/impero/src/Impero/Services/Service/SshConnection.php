<?php namespace Impero\Services\Service;

use Exception;

class SshConnection
{

    /**
     * @var resource
     */
    protected $connection;

    public function __construct($host, $user, $port, $key)
    {
        /**
         * Create connection.
         */
        $this->connection = ssh2_connect($host, $port);

        /**
         * Fingerprint check.
         */
        $keygen = null;
        $command = 'ssh-keygen -lf ' . $key . '.pub -E MD5';
        d("command", $command);
        exec($command, $keygen);
        d("keygen", $keygen);
        $fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
        $publicKeyContent = file_get_contents($key . '.pub');
        $content = explode(' ', $publicKeyContent, 3);
        $calculated = join(':', str_split(md5(base64_decode($content[1])), 2));
        d($calculated, $keygen, $fingerprint);

        if ($fingerprint != $content) {
            d("Wrong server fingerprint");
        }

        /**
         * Authenticate with public and private key.
         */
        $auth = ssh2_auth_pubkey_file($this->connection, $user, $key . '.pub', $key, '');

        /**
         * Throw exception on misconfiguration.
         */
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

    public function close()
    {
        if ($this->connection) {
            ssh2_exec($this->connection, 'exit');
        }

        return $this;
    }

}