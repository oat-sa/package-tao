<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml">

<!--	<xsl:output
		method="html"
		version="4.0"
		indent="yes"
		doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
		media-type="text/html"/>-->

	<xsl:include href="utils.xsl"/>
	<xsl:include href="responseCondition.xsl"/>
	<xsl:include href="generic.xsl"/>
	<xsl:include href="textfield.xsl"/>
	<xsl:include href="list.xsl"/>
	<xsl:include href="matrix.xsl"/>
	<xsl:include href="form.xsl"/>
	<xsl:include href="layout.xsl"/>
	<xsl:include href="multipleItems.xsl"/>
	<xsl:include href="horizontal.xsl"/>
	<xsl:include href="rules.xsl"/>
  <!-- itemGroup -->
	<xsl:template
		name="itemGroup"
		match="itemGroup">
    <div id="itemGroupID" class="invisible"><xsl:value-of select="@id" /></div>
		<div id="container">
			<div id="info_dialog">
				<span id="info_dialog_close">[ <span class="close">-</span> ]</span>
			</div>
			<div id="menuDialog">
				<div class="headerMenuDialog">
					<span id="menuDialogClose">[ <span class="close">-</span> ]</span>
				</div>
			</div>
      <xsl:choose>
        <xsl:when test="@layout='multipleItems'">
          <!--Apply trend item here. CAREFUL not trend !!  multiple items !!! trend template is multiple items template on the same layout so managed in js-->
          <!--trend Items-->
          <!--for the moment only trend, multiple will be done later-->
          <xsl:apply-templates select="." mode="multiple" />
        </xsl:when>
        <!--simples items-->
        <xsl:otherwise>
          <!--3 first instruction management-->
          <xsl:apply-templates select="." mode="generic" />
          <!--NOW specific things by items-->
          <xsl:apply-templates select="item" mode="selectTemplate" />
        </xsl:otherwise>
      </xsl:choose>
		</div>
	</xsl:template>
  <!--item generic management-->
  <xsl:template mode="selectTemplate" match="item">
    <!--<xsl:if test="@layout='information'">-->
      <xsl:choose>
        <!--informational item-->
        <!--<xsl:when test="@layout='information'">-->
          <!--<xsl:apply-templates select="item" mode="information" />-->
        <!--</xsl:when>-->
        <!--item with only one textarea-->
        <xsl:when test="@layout='textfield'">
          <xsl:apply-templates select="." mode="textfield" />
        </xsl:when>
        <!--generic templates (list or matrix)-->
        <xsl:otherwise>
          <xsl:choose>
            <!--horizontal case-->
            <xsl:when test="@disposition='horizontal'">
              <xsl:apply-templates select="." mode="horizontal" />
            </xsl:when>
            <!--matrix case-->
            <xsl:when test="@disposition='matrix'">
              <xsl:apply-templates select="." mode="matrix" />
            </xsl:when>
            <!--list case-->
            <xsl:otherwise>
              <xsl:apply-templates select="." mode="list" />
            </xsl:otherwise>
          </xsl:choose>
        </xsl:otherwise>
      </xsl:choose>
    <!--</xsl:if>-->
  </xsl:template>
  <!--set the input type-->
  <xsl:template match="item" mode="currentItemInputType">
    <xsl:choose>
      <xsl:when test="@layout='slider'">
        <xsl:value-of select="'slider'" />
      </xsl:when>
      <xsl:when test="@layout='simpleMultipleChoiceRadioButton'
         or @layout='complexMultipleChoiceRadioButton'">
        <xsl:value-of select="'radio'" />
      </xsl:when>
      <xsl:when test="@layout='simpleMultipleChoiceCheckbox'
         or @layout='complexMultipleChoiceCheckbox'">
        <xsl:value-of select="'checkbox'" />
      </xsl:when>
      <xsl:when test="@layout='simpleFieldsList'
         or @layout='complexFieldsList'">
        <xsl:value-of select="'text'" />
      </xsl:when>
      <xsl:when test="@layout='simpleDropDown'
         or @layout='complexDropDown'">
        <xsl:value-of select="'select'" />
      </xsl:when>
      <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--add link in head for informational items-->
	<xsl:template name="headInformation">
		<link class="head" rel="stylesheet" type="text/css" href="css/information.css" media="screen" />
	</xsl:template>
  <!--add link in head for slider items-->
  <!--@TODO check the path and also th real utility of this-->
  <xsl:template name="headSlider">
		<link class="head" rel="stylesheet" type="text/css" href="lib/jquery/ui/themes/base/jquery.ui.all.css" media="screen" />
	</xsl:template>
	</xsl:stylesheet>