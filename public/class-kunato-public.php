<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://kunato.ai/
 * @since      1.0.0
 *
 * @package    Kunato
 * @subpackage Kunato/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Kunato
 * @subpackage Kunato/public
 * @author     Kunato <ms@kunato.io>
 */
class Kunato_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kunato_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kunato_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kunato-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kunato_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kunato_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kunato-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Get script URL
	 * 
	 * @since   1.0.0
	 */
	public function get_script_url(){
		$kunato_identification = get_option('kunato_identification'); // User Identifier
		if($kunato_identification == "")
			$kunato_identification = 'generic';

		$script_url = 'https://qx-cdn.sgp1.digitaloceanspaces.com/widget/'.$kunato_identification.'/widget.js?ver=6.0.2';
		return $script_url;
	}

	/**
	 * Check if its an AMP page
	 * @param      array    $plugin  (amp => amp blue plugin, ampforwp => amp red plugin)
	 * @since 1.0.0
	 */
	public function is_amp_page($plugin = array('amp', 'ampforwp')){
		
		// function of AMP blue plugin
		if(in_array('amp', $plugin)){
			if(function_exists('amp_is_request')){
				return amp_is_request();
			}
		}

		// Add a function for AMP red plugin
		if(in_array('ampforwp', $plugin)){
			if(function_exists('ampforwp_is_amp_endpoint')){
				return ampforwp_is_amp_endpoint();
			}
		}
		return false;
	}

	/**
	 * Get kunato option settings
	 * 
	 * @since 1.0.0
	 */
	public function get_kunato_settings(){
		$kunato_identification = (get_option('kunato_identification') != "") ? get_option('kunato_identification') : 'generic'; // User Identifier
			
		$kunato_currency = (get_option('kunato_currency') && get_option('kunato_currency') != '') ? get_option('kunato_currency') : 'q';

		$kunato_json_array = array('identifier' => $kunato_identification, 'currency' => $kunato_currency);
		return $kunato_json_array;
	}


	/**
	 * Inject Custom Scripts
	 *
	 * @since    1.0.0
	 */
	public function script_injection(){
		// if the request is AMP, do not add this script
		if(!$this->is_amp_page()): ?>
			 <script>window.qxSettings = <?php echo json_encode($this->get_kunato_settings()); ?>;</script>
			 <script type='module' src='<?php echo $this->get_script_url(); ?>' id='kunato-widget-js'></script>
		<?php endif;
	}

    /**
	 * Inject Custom head Scripts
	 *
	 * @since    1.0.0
	 */
	public function script_head_injection(){
        $kunato_currency = get_option('kunato_currency'); // User Identifier
        if($kunato_currency == "") { $kunato_currency = 'inr'; }
		if(!$this->is_amp_page()): ?>
			<script> const kunatoConf = { "currency": "<?php echo  esc_js($kunato_currency); ?>" }; </script>
		<?php else: ?>
			<script data-ampdevmode> const kunatoConf = { "currency": "<?php echo  $kunato_currency; ?>" }; </script>
			<script id="qx-price" type="text/plain" target="amp-script">
				const b = document.querySelector(".qx-widget");
				const url = b.getAttribute("data-url");
				const currency = b.getAttribute("data-currency");
        const currency_symbol = currency === 'usd' ? '$' : '₹';

				const elm = document.createElement("div");
				elm.setAttribute("class", "qx-widget-article-price");
				elm.textContent = "valuating...";
				elm.style.backgroundColor = "#e7e7e7";
				elm.style.borderRadius = "4px";
				elm.style.bottom = "auto";
				elm.style.color = "#000";
				elm.style.cursor = "pointer";
				elm.style.display = "inline-block";
				elm.style.fontSize = "13px";
				elm.style.fontWeight = "600";
				elm.style.lineHeight = "25px";
				elm.style.margin = "4px";
				elm.style.maxHeight = "none";
				elm.style.padding = "4px 8px";
				elm.style.position = "relative";
				elm.style.whiteSpace = "nowrap";
				document.body.appendChild(elm);

				const fetchPrice = (url, currency) => {
					const data = {
						currency,
						urls: [url]
					};
					const payload = {
									method: "POST",
									headers: {
										Accept: "application/json",
										"Content-Type": "application/json",
									},
									body: JSON.stringify(data)
									};
					fetch("https://ve2.kunato.ai/price", payload)
					.then(res => res.json())
					.then(data => {
				const price = data[url];
							elm.textContent =  currency_symbol + ' '+ price?.toFixed(2);
					});
				}

				fetchPrice(url, currency);

				setInterval(() => fetchPrice(url, currency), 3000);

				const PollEvent = (initTime,eventId)=>{
					const userId = localStorage.getItem("qxUserId")
					fetch("https://a.kunato.io/event", {
						"headers": {
						"accept": "application/json",
						"content-type": "application/json",
						"user-id": userId
						},
						"body": JSON.stringify({type:"poll",id:eventId}) ,
						"method": "POST",

					}).then((response)=>response.json()).then((res)=>{
						const waitTime = new Date().getTime() - initTime > 600000 ? 60000 : 5000;
						setTimeout(() => {
							PollEvent(initTime,eventId);
						}, waitTime);
					}).catch(err=>{
						console.log(err)
					});
				}

				const PageViewEvent=()=>{
					if(document.querySelector(".main-article")){
						const url = document.querySelector(".main-article")?.getAttribute("data-url")
						const currency = document.querySelector(".main-article")?.getAttribute("data-currency")
						let userId = localStorage.getItem("qxUserId")

						fetch("https://a.kunato.io/event", {
							"headers": {
							"accept": "application/json",
							"content-type": "application/json",
							"user-id": userId?userId:""
							},
							"referrer": "",
							"referrerPolicy": "strict-origin-when-cross-origin",
							"body": JSON.stringify({
								device:{width:window.innerWidth,height:window.innerHeight},type:"pageview",url:url,referrer:null
							}) ,"method": "POST",

						}).then((response)=>response.json()).then((res)=>{
						if(res.ok){
							localStorage.setItem("qxUserId",res.user_id)
							const initTime = new Date().getTime()
							const eventId = res.event_id
							PollEvent(initTime,eventId)
						}
						}).catch(err=>{
							console.log(err)
						});;
					}
				}
				PageViewEvent()
				</script>

		<?php
		endif;
	}

	/**
	 * Inject Custom Scripts in AMP
	 * https://amp.dev/documentation/components/amp-script/
	 * https://amp.dev/documentation/components/amp-bind/
	 * https://amp.dev/documentation/guides-and-tutorials/learn/amp-actions-and-events/
	 * https://github.com/ampproject/amp-wp/blob/4e54b03dce6f360845bfde5dbd19f85cccab7287/includes/amp-helper-functions.php#L945
	 * https://amp.dev/documentation/guides-and-tutorials/develop/custom-javascript/
	 * @since    1.0.0
	 */
	public function script_injection_amp(){ 
		/* <amp-script layout="container" src="https://qx-cdn.sgp1.digitaloceanspaces.com/widget/generic/widget.js?ver=6.0.2" class="i-amphtml-layout-container i-amphtml-element i-amphtml-built" i-amphtml-layout="container">
		</amp-script> */
		if($this->is_amp_page()):?>
			<!--		<amp-script layout="container" src="--><?php //echo $this->get_script_url(); ?><!--"></amp-script>-->
			<!-- <script>window.qxSettings = <?php echo json_encode($this->get_kunato_settings()); ?>;</script> -->
			<!-- <script type='module' src='<?php echo $this->get_script_url(); ?>' id='kunato-widget-js'></script> -->
			<!-- <script async custom-element="amp-script" src="https://cdn.ampproject.org/v0/amp-script-0.1.js"></script> -->
		<?php endif;
	}

	/**
	 * Add kunato widget after post titles
	 *
	 * @since    1.0.0
	 */
	public function wrap_the_title($title, $post_id){
		if(!is_admin()){
		  $kunato_currency = get_option('kunato_currency'); // User Identifier
		  if($kunato_currency == "") { $kunato_currency = 'inr'; }

			if(is_singular() && $this->is_amp_page(array('ampforwp'))){
				return $title;
			}
			/* 
			Add title change only in front-end & for non amp pages
			*/
			$post_title = get_post_field('post_title', $post_id);
			$post_type = get_post_type($post_id);
			
			// Add filter so users can add their own post types. 
			$allowed_post_types = apply_filters('kunato_allowed_post_types', array('post'), array('post'));

			$url = get_permalink($post_id);
//			$url = "https://jantaserishta.com/world/three-killed-in-building-collapse-at-us-airport-1196051";
			$wrapper = $title = '';

      if($this->is_amp_page()){
	      $wrapper .= '<amp-script layout="flex-item" script="qx-price" width="120" height="40">
                    <div class="qx-widget main-article" data-currency="'. $kunato_currency .'" data-url="' . $url . '"></div>
                   </amp-script>';
      } else {
	      	$wrapper .= '<div ';
	      	if(!$this->is_amp_page(array('amp'))){
	      		$wrapper .= 'qx-widget=""';
	      	}
	      	$wrapper .= ' class="qx-widget" data-url="' . $url . '"></div>';
      }

			if(in_array($post_type, $allowed_post_types)){
				if(is_singular()) {
					$title .= $post_title.$wrapper;
				} else{
					/*
					We can only modify the title text. We have no control over anchor tags as most of the theme use their own structure and classes.
					Adding closing anchor tags seems to work for most of the themes.
					*/
					$title .= $post_title."</a>".$wrapper;
				}
			}
			
		}
		return $title;
	}

	/**
	 * Wrap title for ampforwp plugin
	 * 
	 * @since 1.0.0
	 */
	public function wrap_the_title_amp($title){
		if(!is_singular()):
			global $post;
			$url = get_permalink($post->ID);
			$wrapper_div = '<div qx-widget class="qx-widget" data-url="' . $url . '"></div>';
			$wrapper = '<h2 class="amp-post-title">' . wp_kses_data($title) . $wrapper_div . '</h2>'; 
			return $wrapper;
		endif;
		return $title;
	}

	/**
	 * Wrap title for single page ampforwp plugin
	 * 
	 * @since 1.0.0
	 */
	public function wrap_single_post_title_amp($title){
		global $post;
		$url = get_permalink($post->ID);
		$wrapper_div = '<div qx-widget class="qx-widget" data-url="' . $url . '"></div>';
		$wrapper = wp_kses_data($title) . $wrapper_div; 
		return $wrapper;
	}

	public function filter_kses_data($allowed_html_tags, $context){
		$allowed_html_tags['div']['class'] = array();
		$allowed_html_tags['div']['qx-widget'] = array();
		$allowed_html_tags['div']['data-url'] = array();
		$allowed_html_tags['amp-script']['layout'] = array();
		$allowed_html_tags['amp-script']['src'] = array();
			
		return $allowed_html_tags;
	}

	public function add_amp_tag_body_beginning(){
	  if($this->is_amp_page()) {
	    echo '<amp-script layout="container" src="' . esc_url( $this->get_script_url() ) . '" style="opacity:1;">';
    }
	}
	public function add_amp_tag_body_end(){
	  if($this->is_amp_page()) {
	    echo '</amp-script>';
    }
	}
	public function add_amp_sha_meta(){
	  if($this->is_amp_page()) {
//	    echo '<meta name="amp-script-src" content="sha384-' . hash( 'sha384', $this->get_amp_script(), false ) . '">';
	    echo '<meta name="amp-script-src" content="sha384-MEcet8hOieCK5gcINnOl06Vek9zcQ1KMQJReVxHwPNM745GcDfR5Ag0hNLz-yCVC">';
	    echo '<script async src="https://cdn.ampproject.org/v0.js"></script>';
	    echo '<script async custom-element="amp-script" src="https://cdn.ampproject.org/v0/amp-script-0.1.js"></script>';
    }
	}

  public function add_amp_styles (){
	  if($this->is_amp_page()) {
    ?>
    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both;
        }
        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden;
            }
            to {
                visibility: visible;
            }
        }
        @-moz-keyframes -amp-start {
            from {
                visibility: hidden;
            }
            to {
                visibility: visible;
            }
        }
        @-ms-keyframes -amp-start {
            from {
                visibility: hidden;
            }
            to {
                visibility: visible;
            }
        }
        @-o-keyframes -amp-start {
            from {
                visibility: hidden;
            }
            to {
                visibility: visible;
            }
        }
        @keyframes -amp-start {
            from {
                visibility: hidden;
            }
            to {
                visibility: visible;
            }
        }
    </style>
    <noscript><style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none;
            }
      </style></noscript>
    <?php
    }
  }


	public function add_amp_script(){
	  if($this->is_amp_page()) {
	    echo '<script id="qx-price" type="text/plain" target="amp-script">' . $this->get_amp_script() . '</script>';
    }
	}

  private function get_amp_script () {
	  return <<<EOT
    const b = document.querySelector(".qx-widget");
	  const url = b.getAttribute("data-url");
	  const currency = b.getAttribute("data-currency");
    const currency_symbol = currency === 'usd' ? '$' : '₹';

	  const elm = document.createElement("div");
	  elm.setAttribute("class", "qx-widget-article-price");
	  elm.textContent = "valuating...";
	  elm.style.backgroundColor = "#e7e7e7";
	  elm.style.borderRadius = "4px";
	  elm.style.bottom = "auto";
	  elm.style.color = "#000";
	  elm.style.cursor = "pointer";
	  elm.style.display = "inline-block";
	  elm.style.fontSize = "13px";
	  elm.style.fontWeight = "600";
	  elm.style.lineHeight = "25px";
	  elm.style.margin = "4px";
	  elm.style.maxHeight = "none";
	  elm.style.padding = "4px 8px";
	  elm.style.position = "relative";
	  elm.style.whiteSpace = "nowrap";
	  document.body.appendChild(elm);

	  const fetchPrice = (url, currency) => {
		const data = {
			currency,
			urls: [url]
		};
		const payload = {
						method: "POST",
						headers: {
							Accept: "application/json",
							"Content-Type": "application/json",
						},
						body: JSON.stringify(data)
						};
		fetch("https://ve2.kunato.ai/price", payload)
		.then(res => res.json())
		.then(data => {
	 const price = data[url];
				elm.textContent =  currency_symbol + ' '+ price?.toFixed(2);
		});
	  }

	  fetchPrice(url, currency);

	  setInterval(() => fetchPrice(url, currency), 3000);

	  const PollEvent = (initTime,eventId)=>{
		 const userId = localStorage.getItem("qxUserId")
		 fetch("https://a.kunato.io/event", {
			 "headers": {
			   "accept": "application/json",
			   "content-type": "application/json",
			   "user-id": userId
			 },
			 "body": JSON.stringify({type:"poll",id:eventId}) ,
			 "method": "POST",

		   }).then((response)=>response.json()).then((res)=>{
			 const waitTime = new Date().getTime() - initTime > 600000 ? 60000 : 5000;
			 setTimeout(() => {
				 PollEvent(initTime,eventId);
			 }, waitTime);
		   }).catch(err=>{
			 console.log(err)
		   });
	  }

	  const PageViewEvent=()=>{
		 if(document.querySelector(".main-article")){
			 const url = document.querySelector(".main-article")?.getAttribute("data-url")
			 const currency = document.querySelector(".main-article")?.getAttribute("data-currency")
			 let userId = localStorage.getItem("qxUserId")

			 fetch("https://a.kunato.io/event", {
				 "headers": {
				   "accept": "application/json",
				   "content-type": "application/json",
				   "user-id": userId?userId:""
				 },
				 "referrer": "",
				 "referrerPolicy": "strict-origin-when-cross-origin",
				 "body": JSON.stringify({
					 device:{width:window.innerWidth,height:window.innerHeight},type:"pageview",url:url,referrer:null
				 }) ,"method": "POST",

			   }).then((response)=>response.json()).then((res)=>{
			 if(res.ok){
				 localStorage.setItem("qxUserId",res.user_id)
				 const initTime = new Date().getTime()
				 const eventId = res.event_id
				 PollEvent(initTime,eventId)
			 }
			 }).catch(err=>{
				 console.log(err)
			   });;
		 }
	  }
	  PageViewEvent()
EOT;
  }
}
