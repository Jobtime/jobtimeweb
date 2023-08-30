<?php
	class sitemap {

		var $list_url = array();
		
		
		public function add_list( $list_urls ) 
		{
			for( $i = 0; $i < count( $list_urls ); $i++ ) 
			{
				$this->add( $list_urls[$i]['loc'], $list_urls[$i]['priority'], $list_urls[$i]['lastmod'], $list_urls[$i]['changefreq'] );
			}
		}
		
				
		public function add( $url, $lastmod, $priority = 0.5, $changefreq = 'monthly' ) {
			$this->list_url[] = array( 'loc' => $url, 'priority' => $priority, 'lastmod' => $lastmod, 'changefreq' => $changefreq );
		}
		
		public function generate( $file_name = NULL ) {
			$content = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			foreach( $this->list_url as $value ) {
				$content .= '<url><loc>'.htmlspecialchars( utf8_encode( $value['loc'] ) ).'</loc>';
				if( isset( $value['priority'] ) ) $content .= '<priority>'.$value['priority'].'</priority>';
				if( isset( $value['lastmod'] ) ) $content .= '<lastmod>'.$value['lastmod'].'</lastmod>';
				if( isset( $value['changefreq'] ) ) $content .= '<changefreq>'.$value['changefreq'].'</changefreq>';
				$content .= '</url>';
			}
			$content .= '</urlset>';
			if( isset( $file_name ) ) {
				file_put_contents( $file_name, $content );
			}
			return  $content;
		}
	}
?>
