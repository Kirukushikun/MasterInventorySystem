<div>
  <ul>
  	<li><a href="#" wire:click="home">Home</a></li>
  </ul>
  <div>
  	@if($home)
  		<p>Home</p>
  	@endif
  </div>
	<script>
		window.addEventListener('home-clicked', event => {
			alert('Home Clicked')
		});
	</script>
</div>
