<h1><strong>Price</strong> Masterlist</h1>

<p class="menu-nav"><a href="<?php echo URL::site('/inventory') ?>">Back to Inventory</a></p>

<?php if (isset($error_message) && $error_message): ?>
<p class="error"><?php echo $error_message ?></p>
<?php endif ?>

<?php if (isset($success_message) && $success_message): ?>
<p class="success"><?php echo $success_message ?></p>
<?php endif ?>

<div class="span-24">
<?php if (isset($paginator) && $paginator): ?>
	<?php echo $paginator ?>
<?php endif ?>&nbsp;
</div>

<div class="span-24">
	<p class="category-filter">
		<label>Display: </label>
		<?php echo Form::select('category_selector', $categories, $selected_category, array('id' => 'category_selector')) ?>
	</p>
</div>

<div class="reglist-w">
	<table class="reg-list">
		<thead>
			<tr>
				<th>Category</th>
				<th>Name</th>
				<th>Description</th>
				<th>Price</th>
			</tr>
		</thead>
		<tbody>
		<?php if (isset($items) && $items): ?>
		<?php foreach ($items as $key => $row): ?>
			<tr>
				<td><?php echo HTML::chars($row['category_name']) ?></td>
				<td><?php echo HTML::chars($row['name']) ?></td>
				<td><?php echo HTML::chars($row['description']) ?> &nbsp;</td>
				<td id="price-cell-<?php echo $row['id'] ?>" class="price-cell price-editor">
					<span id="price-<?php echo $row['id'] ?>"><?php echo HTML::chars($row['price']) ?></span>
					<input type="text" name="price-editor-<?php echo $row['id'] ?>" id="price-editor-<?php echo $row['id'] ?>" />
					<img id="price-update-spinner-<?php echo $row['id'] ?>" class="price-update-spinner" src="<?php echo URL::site('/media/images/icons/spinner_grey.gif') ?>" alt="Price update spinner" />
				</td>
			</tr>
		<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
</div>