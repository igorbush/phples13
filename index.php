<?php
	require_once "getdb.php";
	$button = 'Добавить';
	if (!empty($_POST['sort_by'])) {
		$sort = $_POST['sort_by'];
		$sql = "SELECT * FROM tasks ORDER BY $sort ASC";
	} else {
		$sql = "SELECT * FROM tasks";
	}

	if (!empty($_GET['id']) && !empty($_GET['action'])) {
		$id= strip_tags($_GET['id']);
		if ($_GET['action'] == "delete") {
			$query = "DELETE FROM tasks WHERE id = ?";
			$del = $dbh->prepare($query);
			$del->bindValue(1, $id, PDO::PARAM_INT);
			$del->execute();
			header("Location: index.php");
		}

		if ($_GET['action'] == "done") {
			$query = "UPDATE tasks SET is_done = 1 WHERE id = ?";
			$done = $dbh->prepare($query);
			$done->bindValue(1, $id, PDO::PARAM_INT);
			$done->execute();
			header("Location: index.php");
		}

		if ($_GET['action'] == "edit") {
			$button = 'Обновить';
			$select = "SELECT description FROM tasks WHERE id = ?";
			$edit = $dbh->prepare($select);
			$edit->bindValue(1, $id, PDO::PARAM_INT);
			$edit->execute();
			foreach ($edit->FetchAll(PDO::FETCH_ASSOC) as $rows) {$str = $rows['description'];}
		}
	}

	if (!empty($_POST['save']) and $_POST['save'] == "Добавить") {
		$description = strip_tags($_POST['description']);
		$insert = "INSERT INTO tasks (description, date_added) VALUES (?, now())";
		$add = $dbh->prepare($insert);
		$add->bindValue(1, $description, PDO::PARAM_STR);
		$add->execute();
	} 
	elseif (!empty($_POST['id']) and !empty($_POST['action']) && $_POST['action'] == 'edit') {
		$button = 'Обновить';
		$id = strip_tags($_POST['id']);
		$description = strip_tags($_POST['description']);
		$insert = "UPDATE tasks SET description = ? WHERE id = ?";
		$update = $dbh->prepare($insert);
		$update->bindValue(1, $description, PDO::PARAM_STR);
		$update->bindValue(2, $id, PDO::PARAM_INT);
		$update->execute();
		header("Location: index.php");
	}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
</head>
<body style="background-color: #9ECCB3">
	<div class="container" style="margin-top: 10vh;">
		<div class="row">
			<div class="col">
				<form action="index.php" method="POST">
					<div class="input-group">
						<input type="hidden" name="id" value="<?= strip_tags($_GET['id']); ?>">
						<input type="hidden" name="action" value="<?= strip_tags($_GET['action']); ?>">
						<input type="text" name="description" placeholder="описание задачи" value="<?= $_GET ? $str : " " ?>" class="form-control">
						<div class="input-group-append">
							<input type="submit" name="save" value="<?= $button; ?>" class="btn btn-info">
						</div>
					</div>
				</form>				
			</div>
			<div class="col">
				<form action="index.php" method="POST">
					<div class="input-group">
						<select name="sort_by" class="custom-select">
							<option>Сортировать по:</option>
							<option value="date_added">Дате добавления</option>
							<option value="is_done">Статусу</option>
							<option value="description">Описанию</option>
						</select>
						<div class="input-group-append">
							<input type="submit" name="sort" value="Отсортировать" class="btn btn-info">
						</div>
					</div>
				</form>	
			</div>
			<table class="table table-bordered table-sm" style="margin-top: 50px; background-color: #fff;">
				<thead class="thead-dark">
					<tr>
						<th scope="col">Описание задачи</th>
						<th scope="col">Дата добавления</th>
						<th scope="col">Статус</th>
						<th scope="col">Редактирование</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($dbh->query($sql) as $row): ?>
					<tr>
						<td><?= $row['description'] ?></td>
						<td><?= $row['date_added'] ?></td>
						<td class="font-weight-bold"><?php echo $result = $row['is_done'] == 0 ? "В процессе" : "Выполнено"; ?></td>
						<td>
							<a href="?id=<?= $row['id'] ?>&action=edit" class="badge badge-primary">Изменить</a>
							<a href="?id=<?= $row['id'] ?>&action=done" class="badge badge-success">Выполнить</a>
							<a href="?id=<?= $row['id'] ?>&action=delete" class="badge badge-danger">Удалить</a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>