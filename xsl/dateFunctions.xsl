<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="2.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="month-by-numeric">
	<xsl:param name="language" select="'en'"/>
	<xsl:param name="form" select="'long'"/>
	<xsl:param name="numeric"/>
	<xsl:value-of select="document('/wwwroot/xxxxxxxxx/canada/xml/xxxdate.xml')/xxxdate/month[@id=$numeric]/language[@id=$language]/form[@id=$form]"/>
</xsl:template>

</xsl:stylesheet>
