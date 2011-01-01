<h1><strong>Price</strong> Lookup</h1>

<p class="menu-nav"><a href="<?php echo URL::site('/inventory') ?>">Back to Inventory</a></p>

<?php if (isset($error_message) && $error_message): ?>
<p class="error"><?php echo $error_message ?></p>
<?php endif ?>

<?php if (isset($success_message) && $success_message): ?>
<p class="success"><?php echo $success_message ?></p>
<?php endif ?>

<div id="form-wrapper" class="span-24 price-edit-form">
	<form action="<?php echo URL::site('/inventory/price') ?>" method="post" enctype="multipart/form-data">
		<div class="span-16">
			<h2>Search an item</h2>
			
			<div class="span-3"><label for="search_key">Enter a keyword</label></div>
			<div class="span-13 last"><input type="text" id="search_key" name="search_key" /></div>
			
			<div class="span-15">
				<div class="reglist-w">
					<table id="item-price-search" class="reg-list">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>Category</th>
								<th>Name</th>
								<th>Description</th>
								<th>Price</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div id="item-price-editform" class="span-8 last">
			<h2>Item price detail</h2>
			
			<div class="span-3"><span>Item name</span></div>
			<div class="span-5 last"><strong id="name">XXX</strong> &nbsp;</div>
			
			<div class="span-3"><span>Description</span></div>
			<div class="span-5 last"><strong id="description">XXX</strong> &nbsp;</div>

			<div class="span-3"><span>Current price</span></div>
			<div class="span-5 last"><strong id="current_price" class="negative">XXX</strong> &nbsp;</div>

			<div class="span-3"><span>Prev effective date</span></div>
			<div class="span-5 last"><strong id="prev_effective_date" class="negative">yyyy-mm-dd</strong> &nbsp;</div>
			
			<div class="span-3"><label for="price">Price</label></div>
			<div class="span-5 last">
				<input type="text" id="price" name="price" /><br />
				<span>format:<em> dddd.dd</em></span>
			</div>
			
			<div class="span-3"><label for="effective_date">Effective date</label></div>
			<div class="span-5 last">
				<input type="text" id="effective_date" name="effective_date" /><br />
				<span>format:<em> yyyy-mm-dd</em></span>
			</div>
			
			<div class="span-3">&nbsp;
				<input type="hidden" name="item_id" id="item_id" />
			</div>
			<div class="span-5 last">
				<button id="cancel-update" class="negative"><?php echo HTML::image('media/images/icons/cross.png') ?> Close</button>
				<button id="submit-update" class="positive"><?php echo HTML::image('media/images/icons/tick.png') ?> Save</button>
				<img id="submit-spinner" src="<?php echo URL::site('/media/images/icons/spinner_grey.gif') ?>" alt="Processing, please wait" />
			</div>		
		</div>
	</form>
</div>
