<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /user/login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO employees(name, phone) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $phone);
    $stmt->execute();

    header("Location: employees.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE employees SET name=?, phone=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $phone, $id);
    $stmt->execute();

    header("Location: employees.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: employees.php");
    exit;
}


$emps = $conn->query("SELECT * FROM employees ORDER BY id DESC");

include_once __DIR__ . '/../includes/header.php';
?>

<h3>Employees</h3>

<div class="card p-3 mb-4">
    <h5>Add Employee</h5>

    <form method="post" class="row g-3 mt-1">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Employee Name" required>
        </div>

        <div class="col-md-4">
            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
        </div>

        <div class="col-md-4">
            <button name="add" class="btn btn-primary w-100">Add</button>
        </div>
    </form>
</div>

<div class="card p-3">
    <h5>Employee List</h5>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php $i=1; while ($e = $emps->fetch_assoc()): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($e['name']); ?></td>
                <td><?= htmlspecialchars($e['phone']); ?></td>

                <td>

                    
                    <button class="btn btn-sm btn-warning" 
                        onclick="editEmp(<?= $e['id']; ?>, '<?= $e['name']; ?>', '<?= $e['phone']; ?>')">
                        Edit
                    </button>

                    
                    <a href="?delete=<?= $e['id']; ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete employee?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>


<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="post">
        <div class="modal-header">
          <h5>Edit Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="empId">

          <label>Name</label>
          <input type="text" name="name" id="empName" class="form-control" required>

          <label class="mt-3">Phone</label>
          <input type="text" name="phone" id="empPhone" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-success">Update</button>
        </div>
      </form>

    </div>
  </div>
</div>
</div></div>
<script>
function editEmp(id, name, phone) {
    document.getElementById("empId").value = id;
    document.getElementById("empName").value = name;
    document.getElementById("empPhone").value = phone;

    new bootstrap.Modal(document.getElementById("editModal")).show();
}
</script>

<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
