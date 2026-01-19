<?php

namespace App\Controllers;

use App\Models\EmployeeModel;

class Profile extends BaseController
{
    public function index()
    {
        if (!session()->get('employee_id')) {
            return redirect()->to('/login');
        }

        $model = new EmployeeModel();
        $user = $model->find(session()->get('employee_id'));

        return view('profile/index', [
            'user' => $user
        ]);
    }

    public function update()
    {
        $model = new EmployeeModel();

        $model->update(session()->get('employee_id'), [
            'employee_name' => $this->request->getPost('employee_name'),
            'phone_no'      => $this->request->getPost('phone_no'),
        ]);

        return redirect()->to('/profile')->with('success', 'Profile updated');
    }

    public function changePassword()
    {
        $model = new EmployeeModel();
        $user = $model->find(session()->get('employee_id'));

        if (!password_verify($this->request->getPost('old_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Old password incorrect');
        }

        if ($this->request->getPost('new_password') !== $this->request->getPost('confirm_password')) {
            return redirect()->back()->with('error', 'Passwords do not match');
        }

        $model->update($user['id'], [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/profile')->with('success', 'Password updated');
    }

    public function uploadImage()
    {
        $file = $this->request->getFile('profile_image');

        if ($file && $file->isValid()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/profile', $newName);

            (new EmployeeModel())->update(session()->get('employee_id'), [
                'profile_image' => $newName
            ]);
           

        }

        return redirect()->to('/profile');
    }
}
