import java.util.ArrayList;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;


public class AlchemyJSONParser
{
	private Gson gson;
	private JsonParser parser;
	private JsonObject obj;
	private String path;
	
	public AlchemyJSONParser(String path)
	{
		this.path = path;
		gson = new Gson();
		parser = new JsonParser();
		obj = (JsonObject)parser.parse(path);
	}

	public ArrayList<Entity> getEntities()
	{
		JsonArray ents = obj.get("entities").getAsJsonArray();
		ArrayList<Entity> entities = new ArrayList<Entity>();
		for(int i = 0; i < ents.size(); i++)
		{
			entities.add(gson.fromJson(ents.get(i).getAsString(), Entity.class));
		}
		
		return entities;
	}
	
	public String getText()
	{
		return obj.get("text").toString();
	}
	
	public String getUrl()
	{
		return obj.get("url").toString();
	}
}
