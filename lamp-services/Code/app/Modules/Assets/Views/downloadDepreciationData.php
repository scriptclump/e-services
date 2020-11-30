<?php

?>
<html>
    <table>
        <tr>
            <?php
            for($i=0;$i<sizeof($headers);$i++)
            {
            ?>
            <td style="background-color:F3F315"><?php echo $headers[$i]; ?></td>
            <?php 
                }
            ?>

        </tr>
        <?php
        if(!empty($data))
        {

            $htmlContainer = '';
            foreach ($data as $value) 
            {

                $varLoopCounter = 0;
                $assetClosingBalance = $value->ActualMRP;
                $assetClosingBalance =round($assetClosingBalance,2);

                // Calculating the Depreciation Percentage
                $Depreciation_percentage = 5/100;
                $Depreciation_percentage = pow($Depreciation_percentage, 1/$value->depresiation_month)-1;
                $Depreciation_percentage = -($Depreciation_percentage);

                for($varLoopCounter=0; (int)$varLoopCounter<$value->DepMonthLoop; $varLoopCounter++){

                    $nextYear1 = $value->PurchaseYear + $varLoopCounter;
                    $nextYear2 = $value->PurchaseYear + $varLoopCounter + 1;

                    $YearEndOn = $varLoopCounter + 1;
                    
                    if($varLoopCounter==0){

                        $depCalLine1 = $assetClosingBalance * $Depreciation_percentage;
                        $depCalc = ($depCalLine1 * $value->DaysDiff) / 364;
                        $depCalc =round($depCalc,2);

                        $tmpClosing = $assetClosingBalance - $depCalc;

                        $tmpClosing = round($tmpClosing,2);


                        $htmlContainer .= "<tr>
                                <td>{$value->ProductName}</td>
                                <td>{$value->purchase_date}</td>
                                <td>{$value->ActualMRP}</td>
                                <td>{$value->depresiation_month}</td>
                                <td>{$value->AssetFlatDep}</td>

                                <td>{$Depreciation_percentage}</td>
                                <td>{$YearEndOn}</td>

                                <td>{$nextYear1} - {$nextYear2}</td>
                                <td>{$value->DaysDiff}</td>
                                <td>{$assetClosingBalance}</td>
                                <td>{$depCalc}</td>
                                <td>{$tmpClosing}</td>
                            </tr>";

                        $assetClosingBalance = $assetClosingBalance-$depCalc;

                    }elseif( $value->DepMonthLoop==($varLoopCounter+1) && $value->DaysLeft>1 ){

                        $depCalc = ($assetClosingBalance - ($value->ActualMRP*5/100));
                        $depCalc =round($depCalc,2);
                        $tmpClosing = $assetClosingBalance-$depCalc;
                        $tmpClosing = round($tmpClosing,2);

                        $htmlContainer .= "<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{$YearEndOn}</td>
                                <td>{$nextYear1} - {$nextYear2}</td>
                                <td>{$value->DaysLeft}</td>
                                <td>{$assetClosingBalance}</td>
                                <td>{$depCalc}</td>
                                <td>{$tmpClosing}</td>
                            </tr>";

                        $assetClosingBalance = $assetClosingBalance-$depCalc;

                    }else{


                        $depCalc = ($assetClosingBalance * 365 * $Depreciation_percentage) / 365;
                        $depCalc =round($depCalc,2);
                        $tmpClosing = $assetClosingBalance - $depCalc;
                        $tmpClosing = round($tmpClosing,2);

                        $htmlContainer .= "<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{$YearEndOn}</td>
                                <td>{$nextYear1} - {$nextYear2}</td>
                                <td>365</td>
                                <td>{$assetClosingBalance}</td>
                                <td>{$depCalc}</td>
                                <td>{$tmpClosing}</td>
                            </tr>";

                        $assetClosingBalance = $assetClosingBalance-$depCalc;

                    }
                }
            }


            echo $htmlContainer;
        }
       
        ?>
    </table>

</html>