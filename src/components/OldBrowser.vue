<template>
    <transition name="fade">
		<div class="old-browsers">
		  <h2>Browser out of date.</h2>
		  <p>It appears you're running on a very old web browser that we're unable to support, and is most likely harmful to your computer. Please choose from any of the following modern browsers. Thanks!</p>
		  <ul>
		    <li>
		      <a href="https://www.google.com/intl/en/chrome/browser/desktop/index.html#brand=CHMB&utm_campaign=en&utm_source=en-ha-na-us-sk&utm_medium=ha">
		        <img src="https://kni-labs.github.io/old-browsers/img/chrome_128x128.png" alt="Google Chrome">
		        <p>Google Chrome</p>
		      </a>
		    </li>
		    <li>
		      <a href="https://www.mozilla.org/en-US/firefox/new/">
		        <img src="https://kni-labs.github.io/old-browsers/img/firefox_128x128.png" alt="Mozilla Firefox">
		        <p>Mozilla Firefox</p>
		      </a>
		    </li>
		
		    <li>
		      <a href="https://support.apple.com/downloads/safari">
		        <img src="https://kni-labs.github.io/old-browsers/img/safari_128x128.png" alt="Safari">
		        <p>Safari</p>
		      </a>
		    </li>
		    <li>
		      <a href="https://www.microsoft.com/en-us/windows/microsoft-edge">
		        <img src="https://kni-labs.github.io/old-browsers/img/edge_128x128.png" alt="Internet Explorer">
		        <p>Microsoft Edge</p>
		      </a>
		    </li>
		  </ul>
		</div>
    </transition>
</template>

<script>

export default {
	name: 'OldBrowser',
  	components: {},
	data () {
		return {
			
		}
	},
	methods: {
		
	},
	
	watch: {
	
	}
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

.old-browsers {
  position: relative;
  background: #fff;
  width: 100%;
  height: 100%;
  font-family: sans-serif;
  font-size: 20px;
  text-align: center;
  padding: 0;
  margin: 0;
  
  h2 { padding: 20px 0 20px 0; }
  p, ul { margin: 0 auto; }
  p {
    max-width: 700px;
    padding-bottom: 50px;
    line-height: 1.4em;    
  }
  ul {
    li {
      display: inline-block;
      padding: 0 25px;
      img { width: 115px; border: 0;}
      p { padding-top: 15px;}
    }
  }
}


</style>