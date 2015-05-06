<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

	<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />

	<!-- itemgroup consistency check -->
	<xsl:template match="itemGroup[consistencyCheck]">
		<div id="container" class="rules">
			<div class="header">Consistency check rule</div>
			<div class="property">
				<span class="name">Rule:</span>
				<span>
					<xsl:value-of select="consistencyCheck/literalRule" />
				</span>
			</div>
			<div class="property">
				<span class="name">Message:</span>
				<span>
					<xsl:value-of select="consistencyCheck/message" />
				</span>
			</div>
			<div class="property">
				<span class="name">Jump:</span>
				<span>
					<xsl:value-of select="consistencyCheck/jump/@ref" />
				</span>
			</div>
		</div>
	</xsl:template>
	
	<!-- itemgroup branching rule -->
	<xsl:template match="itemGroup[branchingRule]">
		<div id="container" class="rules">
			<div class="header">Branching rule</div>
			<div class="property">
				<span class="name">Rule:</span>
				<span>
					<xsl:value-of select="branchingRule/literalRule" />
				</span>
			</div>
		</div>
	</xsl:template>
	
	<!-- itemgroup derivated variable -->
	<xsl:template match="itemGroup[derivatedVariable]">
		<div id="container" class="rules">
			<div class="header">Derivated variable rule</div>
			<div class="property">
				<span class="name">Rule:</span>
				<span>
					<xsl:value-of select="derivatedVariable/literalRule" />
				</span>
			</div>
		</div>
	</xsl:template>

</xsl:stylesheet>