--drop view dbo.rndView;;

CREATE VIEW dbo.rndView
AS
SELECT round(RAND()*10000,0) rndResult
;;
--drop function dbo.generis_sequence_uri_provider;;
create function dbo.generis_sequence_uri_provider(@modelUri varchar(250)) 
returns char(255) 
as 
begin 
	
	declare @stamp INT
	declare @lastval INT
	
	set @stamp = DATEDIFF(SECOND,'1970-01-01', getdate()) 
	 
	-- this is better but work for 2012+
	--set @lastval = SELECT NEXT VALUE FOR sequence_uri_provider

	--not nice but work in 2008
	set @lastval = (select max(uri_sequence) from sequence_uri_provider)
	
	declare @i int 
	SELECT @i = rndResult FROM rndView 
	if @lastval is null set @lastval = DATEPART(ms, GETDATE())
	return @modelUri + 'i'  + convert(varchar(50),@stamp) + convert(varchar(50),@i)  + convert(varchar(50),@lastval)
	
end