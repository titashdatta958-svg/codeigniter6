<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class DepartmentFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('system_role') ?? '';
        $uri  = service('uri');
        $path = $uri->getPath();

        if (in_array($role, ['Super Manager', 'Manager'])) {
            return;
        }

        if (str_starts_with($path, 'team-builder/destinations') || str_starts_with($path, 'team-builder/zone') || str_starts_with($path, 'zones')) {
            return;
        }

        $dept = $request->getVar('department_id');

        if (!$dept) {
            $segments = $uri->getSegments(); // SAFE array of segments

            if (isset($segments[0], $segments[1], $segments[2]) && $segments[0] === 'team-builder' && $segments[1] === 'get-data') {
                $dept = $segments[2];
            }
        }

        if (!$dept) {
            if (in_array(strtoupper($request->getMethod()), ['GET'])) {
                return;
            }

            return redirect()
                ->to('/team-builder')
                ->with('error', 'Forbidden - department required');
        }

        $sessionDept = session()->get('department_id');

        if ((string)$sessionDept !== (string)$dept) {

            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Forbidden - department mismatch'
                    ])
                    ->setStatusCode(403);
            }

            // Redirect for normal requests
            return redirect()
                ->to('/team-builder')
                ->with('error', 'Forbidden - department mismatch');
        }

        // ✅ If department matches → allow request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}
