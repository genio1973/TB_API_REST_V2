<?php
class AuthenticateApiKey
{
    /**
    * Example middleware invokable class
    *
    * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
    * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
    * @param  callable                                 $next     Next middleware
    *
    * @return \Psr\Http\Message\ResponseInterface
    */

    private $role;
    private $role_admin;

    public function __construct($role, $role_admin)
    {
        $this->role = $role;
        $this->role_admin = $role_admin;
    }

    public function __invoke($request, $response, $next)
    {
        // Obtenir les en-têtes de requêtes
        $headers = $request->getHeaders();
        
        $data = array();

        // Vérification de l'en-tête d'autorisation
        if (isset($headers['HTTP_APIKEY'])) {
            $db = new DbHandler();

            // Obtenir la clé d'api
            $api_key = $headers['HTTP_APIKEY'][0];
            //Obtenir l'id
            $id = $headers['HTTP_USERID'][0];

            // Valider la clé API pour un responsable
            $res = $db->isValidRoleApiKeyWithID($api_key, $id, $this->role);
            if (!$res) { //  Clé API n'est pas présente dans la table des utilisateurs
                $res = $db->isValidRoleApiKeyWithID($api_key, $id, $this->role_admin);
                 if (!$res) { //  Clé API n'est pas présente dans la table des utilisateurs pour un administrateur
                    $data['error'] = true;
                    $data['message'] = "401";
                    $data['result'] = "Accès Refusé. Clé APIKEY ($api_key) avec l'id ($id) : invalide";
                    return echoRespnse(401, $response, $data);
                 }

            }
        } else {
            // Clé API est absente dans la en-tête
            $data["error"] = true;
            $data["message"] = "400";
            $data['result'] = "Clé API est manquante";
            return echoRespnse(400, $response, $data);
        }
       
        $response = $next($request, $response);

        return $response;
    }


}
