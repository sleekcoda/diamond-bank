<div id="<?php echo $objects['year']; ?>" class="report_content" style="padding-left:20px;">
    <h3>Year <?php echo $objects['year']; ?></h3>

        <h4>Annual</h4>
        <ul>
            <?php $i=0; while($i <= sizeof($object)): ?>

                <?php  ?>
                <?php  if($object[$i]->category == 'annual'): ?>
                    <li style="background-image:none;">
                        <a class="download-icon" href="<?php echo (wp_get_attachment_url($object[$i]->id) ===false) ? '#': wp_get_attachment_url($object[$i]->id) ; ?>"><strong><?php echo $object[$i]->title; ?></strong>&nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                    </li>
                <?php endif; ?>
            <?php $i++;endwhile; ?>
        </ul>

        <hr><h4>Quarterly</h4>
        <ul>
            <?php $i=0;while($i <= sizeof($object)): ?>
            
                <?php if($object[$i]->category == 'quarterly'): ?>
                        <li style="background-image:none;">
                            <a class="download-icon" href="<?php echo (wp_get_attachment_url($object[$i]->id) ===false) ? '#': wp_get_attachment_url($object[$i]->id) ; ?>"><strong><?php echo $object[$i]->title; ?></strong>&nbsp;&nbsp;<i class="fa fa-download"></i>
                            </a>
                        </li>
                <?php endif; ?>
            <?php $i++;endwhile; ?>
        </ul>

        <hr><h4>Abridged</h4>
        <ul>
            <?php $i=0;while($i <= sizeof($object)): ?>
        
            <?php if($object[$i]->category == 'abridge'): ?>
                    <li style="background-image:none;">
                        <a class="download-icon" href="<?php echo (wp_get_attachment_url($object[$i]->id) ===false) ? '#': wp_get_attachment_url($object[$i]->id) ; ?>"><strong><?php echo $object[$i]->title; ?></strong>&nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                    </li>
            <?php endif; ?>

            <?php $i++;endwhile; //endforeach; ?>
        </ul> 

    
</div>