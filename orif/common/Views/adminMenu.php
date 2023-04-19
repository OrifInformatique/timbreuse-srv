<div id="admin-menu" class="container">
    <nav>
        <div class="nav nav-pills">
            <?php foreach (config('\Common\Config\AdminPanelConfig')->tabs as $tab): ?>
                <a href="<?=base_url($tab['pageLink'])?>" class="nav-link adminnav" ><?=lang($tab['label'])?></a>
            <?php endforeach ?>
        </div>
    </nav>
</div>
<script defer>
    document.querySelectorAll('.adminnav').forEach((nav)=>{
        if (nav.href.includes(window.location)){
            nav.classList.add('active')
        }
        else{
            nav.classList.remove('active')
        }
    })
</script>
