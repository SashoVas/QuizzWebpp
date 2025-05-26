//this script toggles the state of checkboxes during registration

function toggleAdminCheckbox(checkbox) {
    const adminCheckbox = document.getElementById('role-admin');
    if (checkbox.checked) {
        adminCheckbox.disabled = true;
    } else {
        const studentChecked = document.getElementById('role-student').checked;
        const teacherChecked = document.getElementById('role-teacher').checked;
        if (!studentChecked && !teacherChecked) {
            adminCheckbox.disabled = false;
        }
    }
}

function toggleOtherCheckboxes(adminCheckbox) {
    const studentCheckbox = document.getElementById('role-student');
    const teacherCheckbox = document.getElementById('role-teacher');
    if (adminCheckbox.checked) {
        studentCheckbox.disabled = true;
        teacherCheckbox.disabled = true;
    } else {
        studentCheckbox.disabled = false;
        teacherCheckbox.disabled = false;
    }
}