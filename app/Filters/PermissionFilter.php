<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('system_role') ?? '';
        $uri  = service('uri')->getPath(); // current URI path

        // --- SUPER MANAGER ---
        if ($role === 'Super Manager') {
            // Full access
            return;
        }

        // --- MANAGER ---
        if ($role === 'Manager') {
            // Managers cannot access register page or process registration
            if (str_contains($uri, 'auth/register') || str_contains($uri, 'auth/process_register')) {
                return $this->forbidden($request);
            }
            // All other actions allowed
            return;
        }

        // --- MEMBER / INTERN ---
        if (in_array($role, ['Member', 'Intern'])) {

            // Only allow viewing specific pages
            $allowedViewPages = [
                'team-builder',
                'team-builder/current-team',     // your route for current team structure
                'team-builder/destinations',     // destination list
                'team-builder/zones',            // zone destination mapping
            ];

            $allowed = false;
            foreach ($allowedViewPages as $path) {
                if (str_starts_with($uri, $path)) {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                return $this->forbidden($request);
            }

            // Block any modifying HTTP method
            $method = strtoupper($request->getMethod());
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                return $this->forbidden($request);
            }
        }

        // --- ROUTE-SPECIFIC ROLE CHECK ---
        if (!empty($arguments)) {
            $allowed = is_array($arguments) ? $arguments : explode(',', $arguments);
            $allowed = array_map('trim', $allowed);
            if (!in_array($role, $allowed)) {
                return $this->forbidden($request);
            }
        }
    }

    protected function forbidden(RequestInterface $request)
    {
        if ($request->isAJAX()) {
            $response = service('response');
            return $response
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized'])
                ->setStatusCode(403);
        }

        return redirect()->to('/team-builder')->with('error', 'Unauthorized');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}
