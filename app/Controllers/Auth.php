<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DepartmentModel;

class Auth extends BaseController
{
    // ----------------------------------------------------
    // LOGIN
    // ----------------------------------------------------
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/team-builder');
        }
        return view('auth_login');
    }

    public function process_login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return $this->request->isAJAX()
                ? $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ])
                : redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model    = new EmployeeModel();
        $email    = $this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {

            // ✅ STANDARDIZED SESSION
session()->set([
    'isLoggedIn'     => true,
    'employee_id'    => $user['id'],
    'employee_name'  => $user['employee_name'],
    'department_id'  => $user['department_id'],
    'system_role'    => $user['system_role'],   // ENUM ONLY

    // Derived flags (DO NOT STORE IN DB)
    'isSuperManager' => $user['system_role'] === 'Super Manager',
    'isManager'      => $user['system_role'] === 'Manager',
    'isViewer'       => in_array($user['system_role'], ['Member','Intern']),
]);




            return $this->request->isAJAX()
                ? $this->response->setJSON(['status' => 'success'])
                : redirect()->to('/team-builder')->with(
                    'success',
                    'Welcome back, ' . $user['employee_name']
                );
        }

        return $this->request->isAJAX()
            ? $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid Email or Password'
            ])
            : redirect()->back()->with('error', 'Invalid Email or Password');
    }

    // ----------------------------------------------------
    // REGISTRATION
    // ----------------------------------------------------
    public function register()
    {
        if (session('system_role') !== 'Super Manager') {
            return redirect()->to('/team-builder')->with('error', 'Unauthorized');
        }

        $deptModel = new DepartmentModel();

        return view('auth_register', [
            'departments' => $deptModel->findAll(),
            'isSuperManager' => true
        ]);
    }

    public function process_register()
    {
        if (session('system_role') !== 'Super Manager') {
            return $this->request->isAJAX()
                ? $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])
                : redirect()->to('/team-builder')->with('error', 'Unauthorized');
        }

        $rules = [
            'employee_name' => 'required|min_length[3]|max_length[100]',
            'email'         => 'required|valid_email|is_unique[izifiso_employee.email]',
            'password'      => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'phone_no'      => 'required|numeric|min_length[10]',
            'department_id' => 'required',
            'designation'   => 'permit_empty|alpha_numeric_space',
            'system_role'   => 'permit_empty' // optional, only set by Super Manager
        ];

        $errors = [
            'password' => [
                'regex_match' => 'Password must contain at least one uppercase, one lowercase, and one number.'
            ],
            'email' => [
                'is_unique' => 'This email address is already registered.'
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            return $this->request->isAJAX()
                ? $this->response->setJSON(['status'=>'error','errors'=>$this->validator->getErrors()])
                : redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $empModel = new EmployeeModel();

        $currentRole = session('system_role');
        $systemRole = ($currentRole === 'Super Manager' && $this->request->getPost('system_role'))
            ? $this->request->getPost('system_role')
            : 'Member'; // default

        $empModel->insert([
            'employee_name' => $this->request->getPost('employee_name', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'         => $this->request->getPost('email', FILTER_SANITIZE_EMAIL),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'phone_no'      => $this->request->getPost('phone_no', FILTER_SANITIZE_NUMBER_INT),
            'department_id' => $this->request->getPost('department_id'),
            'designation'   => $this->request->getPost('designation', FILTER_SANITIZE_SPECIAL_CHARS),
            'location'      => $this->request->getPost('location', FILTER_SANITIZE_SPECIAL_CHARS),
            'system_role'   => $systemRole
        ]);

        return $this->request->isAJAX()
            ? $this->response->setJSON(['status'=>'success','message'=>'Registration successful!'])
            : redirect()->to('/auth/login')->with('success','Registration successful! You can now login.');
    }

    // ----------------------------------------------------
    // LOGOUT
    // ----------------------------------------------------
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
