<h1><strong>Item</strong> Masterlist</h1>

<p class="menu-nav"><a href="<?php echo URL::site('/inventory') ?>">Back to Inventory</a></p>

<?php if (isset($error_message) && $error_message): ?>
<p class="error"><?php echo $error_message ?></p>
<?php endif ?>

<?php if (isset($success_message) && $success_message): ?>
<p class="success"><?php echo $success_message ?></p>
<?php endif ?>

<div class="span-20">
<?php if (isset($paginator) && $paginator): ?>
	<?php echo $paginator ?>
<?php endif ?>&nbsp;
</div>

<div class="span-4 last">
	<p class="crud-add"><a href="<?php echo URL::site('/inventory/item/add') ?>">Add item</a></p>
</div>

<div class="reglist-w">
	<table class="reg-list">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Code Name</th>
				<th>Name</th>
				<th>Description</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php if (isset($items) && $items): ?>
		<?php foreach ($items as $key => $row): ?>
			<tr>
				<td class="crud-edit"><a href="<?php echo URL::site('/inventory/item/edit/'.$row['id']) ?>">Edit</a></td>
				<td><?php echo HTML::chars($row['code_name']) ?></td>
				<td><?php echo HTML::chars($row['name']) ?></td>
				<td><?php echo HTML::chars($row['description']) ?> &nbsp;</td>
				<td class="crud-delete"><a href="<?php echo URL::site('/inventory/item/delete/'.$row['id']) ?>">Delete</a></td>
			</tr>
		<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
</div>
<?php echo View::factory('site/predelete') ?>