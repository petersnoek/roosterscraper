<?php $__env->startSection('content'); ?>
<h1>Gevonden roosters:</h1>

    <ul>
    <?php foreach($docenten as $d): ?>
        <li><a href="<?php echo e($d['roosterurl']); ?>"><?php echo e($d['docent']); ?></a></li>
    <?php endforeach; ?>
    </ul>

<h1>Gevonden lessen:</h1>
<ul>
    <?php foreach($lessenContainer->lessen as $les): ?>
        <li><?php echo e($les->docent); ?> <?php echo e($les->dag); ?> <?php echo e($les->lescode); ?> <?php echo e($les->starttijd); ?>-<?php echo e($les->eindtijd); ?>

            <?php echo e($les->GetKlassen()); ?>  <?php echo e($les->GetLokalen()); ?> </li>
    <?php endforeach; ?>
</ul>

<h1>Planbord</h1>

<?php foreach($dagen as $dag): ?>
<h2><?php echo e($dag); ?></h2>
<table class="roostertabel">
    <tr>
        <th class="nr">#</th>
        <th class="tijd">van</th>
        <th class="tijd">tot</th>
        <?php foreach($klassen as $k): ?>
        <th><?php echo e($k[1]); ?></th>
        <?php endforeach; ?>
        <th class="separator"></th>
        <?php foreach($docenten as $d): ?>
            <th><?php echo e($d['docent']); ?></th>
        <?php endforeach; ?>
        <th class="separator"></th>
        <?php foreach($lokalen as $l): ?>
            <th><?php echo e($l[1]); ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach($tijden as $tijd): ?>
    <tr>
        <td class="nr"><?php echo e($tijd[0]); ?></td>
        <td class="tijd"><?php echo e($tijd[1]); ?></td>
        <td class="tijd"><?php echo e($tijd[2]); ?></td>
        <?php foreach($klassen as $k): ?>
            <td><?php echo e($lessenContainer->ZoekDocent($dag, $tijd[1], $k[0])); ?></td>
        <?php endforeach; ?>
        <td class="separator"></td>
        <?php foreach($docenten as $d): ?>
            <td><?php echo e($lessenContainer->ZoekLes($dag, $tijd[1], $d['docent'])); ?></td>
        <?php endforeach; ?>
        <td class="separator"></td>
        <?php foreach($lokalen as $l): ?>
            <td><?php echo e($l[1]); ?></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php endforeach; ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('_layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>