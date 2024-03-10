 <!-- Header-->
 <header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Announcements</h1>
        </div>
    </div>
</header>
<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body">
               <?php
               $qry = $conn->query("SELECT * FROM `announcements` order by unix_timestamp(date_created) desc");
               while($row = $qry->fetch_assoc()):
                    $row['announcement'] = strip_tags(stripslashes(html_entity_decode($row['announcement'])))

                ?>
                <a class="card text-dark card-outline card-primary mb-2 view_data" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' data-title='<?php echo $row['title'] ?>' >
                    <div class="card-header">
                        <h5 class="card-title"><?php echo $row['title'] ?></h5>
                        <span class="float-right text-muted"><?php echo date("M d,Y h:i A",strtotime($row['date_created'])) ?></span>
                    </div>
                    <div class="card-header">
                        <p class="truncate"><?php echo $row['announcement'] ?></p>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('.view_data').click(function(){
            uni_modal($(this).attr('data-title'),'./view_accouncement.php?id='+$(this).attr('data-id'),'mid-large')
        })
    })
</script>