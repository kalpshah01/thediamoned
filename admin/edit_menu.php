<?php
session_start();
if (!isset($_SESSION['admin_id'])) header('Location: login.php');
require_once __DIR__ . '/../config/db.php';
$id = intval($_GET['id'] ?? 0); if (!$id) header('Location: manage_menu.php');
$stmt = $conn->prepare('SELECT * FROM menu_items WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $item = $stmt->get_result()->fetch_assoc();
if (!$item) header('Location: manage_menu.php');
$errors=[]; if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name=trim($_POST['name']??''); $category=trim($_POST['category']??'Main'); $price=trim($_POST['price']??''); $description=trim($_POST['description']??'');
  if ($name==='') $errors[]='Name required'; if ($price==='' || !is_numeric($price)) $errors[]='Valid price';
  $image_path=$item['image'];
  if (!empty($_FILES['image']['name'])) {
    $finfo=pathinfo($_FILES['image']['name']); $ext=strtolower($finfo['extension'] ?? ''); $allowed=['jpg','jpeg','png','webp','gif']; $max=2*1024*1024;
    if (!in_array($ext,$allowed)) $errors[]='Invalid image'; elseif ($_FILES['image']['size']>$max) $errors[]='Too large';
    else {
      $new='assets/images/menu/'.time().'_'.bin2hex(random_bytes(6)).'.'.$ext;
      if (!is_dir(__DIR__.'/../assets/images/menu')) mkdir(__DIR__.'/../assets/images/menu',0755,true);
      if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../'.$new)) {
        if (!empty($item['image']) && file_exists(__DIR__.'/../'.$item['image'])) @unlink(__DIR__.'/../'.$item['image']);
        $image_path=$new;
      } else $errors[]='Upload failed';
    }
  }
  if (empty($errors)) {
    $up = $conn->prepare('UPDATE menu_items SET name=?,category=?,price=?,description=?,image=? WHERE id=?');
    $up->bind_param('ssdssi',$name,$category,$price,$description,$image_path,$id);
    if ($up->execute()) header('Location: manage_menu.php?updated=1'); else $errors[]='DB error';
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Edit Dish</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/style.css" rel="stylesheet"></head><body class="bg-light"><div class="container container-max py-4"><div class="card p-3 shadow-sm"><h3>Edit Dish</h3><?php if(!empty($errors)) echo '<div class="alert alert-danger">'.implode('<br>',$errors).'</div>'; ?><form method="post" enctype="multipart/form-data"><div class="mb-3"><label>Name</label><input class="form-control" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required></div><div class="mb-3"><label>Category</label><input class="form-control" name="category" value="<?php echo htmlspecialchars($item['category']); ?>"></div><div class="mb-3"><label>Price</label><input class="form-control" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required></div><div class="mb-3"><label>Replace Image</label><input class="form-control" type="file" name="image"><?php if(!empty($item['image']) && file_exists(__DIR__.'/../'.$item['image'])) echo '<div class="mt-2"><img src="/'.htmlspecialchars($item['image']).'" style="height:90px;object-fit:cover;border-radius:6px"></div>'; ?></div><div class="mb-3"><label>Description</label><textarea class="form-control" name="description"><?php echo htmlspecialchars($item['description']); ?></textarea></div><button class="btn btn-primary">Save</button> <a class="btn btn-secondary" href="manage_menu.php">Cancel</a></form></div></div></body></html>