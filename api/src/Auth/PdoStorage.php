<?php
namespace Auth;

use OAuth2\Storage\Pdo;

class PdoStorage extends Pdo
{
    protected function checkPassword($user, $password)
    {
        return password_verify($password, $user['password']);
    }

    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $stmt = $this->db->prepare(sprintf('SELECT * from %s
            where client_id = :client_id', $this->config['client_table']));
        $stmt->execute(compact('client_id'));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result && password_verify($client_secret, $result['client_secret']);
    }
}
