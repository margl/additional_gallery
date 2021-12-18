<div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="1000">
    <div class="carousel-inner">
        {foreach from=$additionalImages item=$image name=gallery_loop}
            <div class="carousel-item {if $smarty.foreach.gallery_loop.first}active{/if}">
                <img class="d-block w-100" src="{$image->uri}">
            </div>
        {/foreach}
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>