<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<responseCondition>
    <responseIf>
        <<?=$condition?>>
<?php if(!empty($responseIdentifier) && $map):?>
        <mapResponse identifier="<?=$responseIdentifier?>" />
<?php elseif(!empty($responseIdentifier) && $mapPoint):?>
    <mapResponsePoint identifier="<?=$responseIdentifier?>" />
<?php else:?>
        <variable identifier="<?=$outcomeIdentifier?>" />
<?php endif;?>
            <baseValue baseType="float"><?=$value?></baseValue>
        </<?=$condition?>>
        <setOutcomeValue identifier="<?=$feedbackOutcomeIdentifier?>">
<baseValue baseType="identifier"><?=$feedbackIdentifierThen?></baseValue>
        </setOutcomeValue>
    </responseIf>
<?php if(!empty($feedbackIdentifierElse)):?>
            <responseElse>
                <setOutcomeValue identifier="<?=$feedbackOutcomeIdentifier?>">
                <baseValue baseType="identifier"><?=$feedbackIdentifierElse?></baseValue>
                        </setOutcomeValue>
                </responseElse>
<?php endif;?>
</responseCondition>
