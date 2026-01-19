<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
 public function before(RequestInterface $request, $arguments = null)
{
    // Fix 1: Use the correct session key
    if (!session()->get('isLoggedIn')) {
        
        if ($request->isAJAX()) {
            return service('response')->setJSON(['status' => 'expired'])->setStatusCode(401);
        }

        // Fix 2: Redirect to 'auth/login' (the route you actually defined)
        return redirect()->to('/auth/login'); 
    }
}
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}