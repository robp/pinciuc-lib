<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="2.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template name="page-number">
    <xsl:param name="current" select="1"/>
    <xsl:param name="selected" select="1"/>
    <xsl:param name="numpages" select="1"/>

    <xsl:choose>
      <xsl:when test="$current = $selected">
        <option value="{$current}" selected="selected"><xsl:value-of select="$current"/></option>
      </xsl:when>
      <xsl:otherwise>
        <option value="{$current}"><xsl:value-of select="$current"/></option>
      </xsl:otherwise>
    </xsl:choose>

    <xsl:if test="$current &lt; $numpages">
      <xsl:call-template name="page-number">
        <xsl:with-param name="current" select="$current + 1"/>
        <xsl:with-param name="selected" select="$selected"/>
        <xsl:with-param name="numpages" select="$numpages"/>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>
