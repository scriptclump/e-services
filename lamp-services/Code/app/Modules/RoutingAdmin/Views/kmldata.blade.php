<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<kml xmlns="http://earth.google.com/kml/2.1">
<!-- Data derived from:
       Ed Knittel - || tastypopsicle.com
       Feel free to use this file for your own purposes.
       Just leave the comments and credits when doing so.
-->
  <Document>
    <name>Test</name>
    <description>Route History Data</description>

    
    <Style id="redLine">
      <LineStyle>
        <color>ff0000ff</color>
        <width>4</width>
      </LineStyle>
    </Style>

    <Placemark>
      <name>Red Line</name>
      <styleUrl>#redLine</styleUrl>
      <LineString>
        <altitudeMode>relative</altitudeMode>
        <coordinates>
            @foreach ($coordinatesData as $coordinates)
                asdasdas "\n"
            @endforeach 

        </coordinates>
      </LineString>
    </Placemark>
  </Document>
</kml>