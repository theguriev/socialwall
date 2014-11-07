<?php
namespace Wall;

class Page{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $walls;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		$this->initControls();
		add_action('admin_menu', array(&$this, 'addPage'));
	}

	public function addPage()
	{
			$page = add_menu_page(
		        'GC Social Wall', 
		        'GC Social Wall', 
		        'administrator', 
		        'gc_social_wall', 
		        array(&$this, 'getPageHTML')
		    );   
		    add_action('admin_print_scripts-' . $page, array(&$this, 'pageScriptsAndStyles'));
	}

	public function pageScriptsAndStyles()
	{
		// ==============================================================
		// Scripts
		// ==============================================================
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-dr' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'base64', GCLIB_URL.'/js/base64.js');
		wp_enqueue_script( 'gc_social_wall_admin', GCLIB_URL.'/js/gc_social_wall_admin.js', array('jquery'));
		wp_localize_script( 'gc_social_wall_admin', 'defaults', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			) 
		);
		wp_localize_script( $handle, $object_name, $l10n );
		// ==============================================================
		// Styles
		// ==============================================================
		wp_enqueue_style( 'gc-social-wall-admin', GCLIB_URL.'/css/admin.css' );
	}

	public function initControls()
	{
		$this->walls = array(
			Walls::getFacebook(), 
			Walls::getTwitter(),
			Walls::getPost(),
			Walls::getYouTube(),
			Walls::getVimeo(),
			Walls::getInstagram(),
			Walls::getVK(),
		);
	}

	public function wrapWall($wall)
	{
		ob_start();
		?>
		<div class="widget ui-draggable" data-type="<?php echo $wall->getName(); ?>">
			<div class="widget-top">
				<div class="widget-title-action">
					<a href="#available-widgets" class="widget-action hide-if-no-js"></a>
					<a href="" class="widget-control-edit hide-if-js">
						<span class="edit">Edit</span>
						<span class="add">Add</span>
						<span class="screen-reader-text"><?php echo $wall->getTitle(); ?></span>
					</a>
				</div>
				<div class="widget-title">
					<h4>
						<?php echo $wall->getTitle(); ?>
						<span class="in-widget-title"></span>
					</h4>
				</div>
			</div>

			<div class="widget-inside">
				<form method="post" action="" data-type="<?php echo $wall->getName(); ?>">
					<div class="widget-content">
						<?php echo $wall->getHTML(); ?>
					</div>

					<div class="widget-control-actions">
						<div class="alignleft">
							<a href="#remove" class="widget-control-remove">Delete</a>
							<a href="#close" class="widget-control-close">Close</a>
						</div>
						<div class="alignright">
							<input type="submit" value="Save" class="button button-primary widget-control-save right" id="widget-archives-__i__-savewidget" name="savewidget">
							<span class="spinner"></span>
						</div>
						<br class="clear"></div>
				</form>
			</div>

			<div class="widget-description"><?php echo $wall->getDescription(); ?></div>
		</div>
		<?php
		
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}

	public function getPageHTML()
	{
		?>
		<div class="gc-social-wall-admin">
			<h2>GC Social Wall Shortcode generator</h2>

			<div class="widget-liquid-left">
				<div id="widgets-left">
					<div class="widgets-holder-wrap ui-droppable" id="available-widgets">
						<div class="sidebar-name">
							<div class="sidebar-name-arrow">
								<br></div>
							<h3>
								Available Walls
								<span id="removing-widget">
									Deactivate
									<span></span>
								</span>
							</h3>
						</div>
						<div class="widget-holder">
							<div class="sidebar-description">
								<p class="description">
									To activate a wall drag it to a sidebar or click on it. To deactivate a widget and delete its settings, drag it back.
								</p>
							</div>
							<div id="widget-list">
								<?php
								foreach ($this->walls as $wall) 
								{
									echo $this->wrapWall($wall);
								}
								?>
							</div>
							<br class="clear">
						</div>
						<br class="clear">
					</div>
				</div>
			</div>
			<div class="widget-liquid-right">
				<div id="widgets-right">
					<div class="sidebars-column-1">
						<div class="widgets-holder-wrap">
							<div class="widgets-sortables ui-sortable" id="short-code-sortable">
								<div class="sidebar-name">
									<h3>
										Short code generator
										<span class="spinner"></span>
									</h3>
								</div>
								<div class="sidebar-description">
									<p class="description">
										Insert the desired social network and configure it.
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br class="clear">
			<div class="shortcode">
				<h3>Copy your shortcode</h3><a href="#select-all" class="select-all" onclick="Walls.codeSelect();">Select All</a>
				<hr>
				<code id="shortcode-text">
					[gc_social_wall]	
				</code>
			</div>
		</div>
		<?php
	}
}