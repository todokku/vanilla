<?php if (!defined('APPLICATION')) exit(); ?>
    <h2 class="H"><?php echo $this->Data('Title'); ?></h2>
<?php
echo $this->Form->open();
echo $this->Form->errors();
?>
    <ul>
        <?php if ($this->Data('ForceEditing') && $this->Data('ForceEditing') != FALSE) { ?>
            <div
                class="Warning"><?php echo sprintf(T("You are editing %s's quote settings"), $this->Data('ForceEditing')); ?></div>
        <?php } ?>
        <li>
            <?php
            echo $this->Form->label('Quote Folding', 'QuoteFolding');
            echo wrap(t('How many levels deep should we start folding up quote trees?'), 'div');
            echo $this->Form->DropDown('QuoteFolding', $this->Data('QuoteFoldingOptions'));
            ?>
        </li>
    </ul>
<?php echo $this->Form->close('Save', '', array('class' => 'Button Primary'));
