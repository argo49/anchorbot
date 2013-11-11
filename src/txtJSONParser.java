import java.io.File;
import java.io.IOException;
import java.util.ArrayList;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import com.sun.org.apache.xerces.internal.parsers.DOMParser;

import org.w3c.dom.Element;


public class txtJSONParser {

	private Gson gson;
	private JsonParser parser;
	private JsonObject obj;
	private String path;
	
	private String url;
	private ArrayList<String> paras = new ArrayList<String>();
	
	public txtJSONParser(String path) throws ParserConfigurationException, SAXException, IOException
	{
		this.path = path;	
			
		File fXmlFile = new File(path);
		DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder dBuilder = dbFactory.newDocumentBuilder();
		Document doc = dBuilder.parse(fXmlFile);
		
		doc.getDocumentElement().normalize();
		NodeList nList = doc.getElementsByTagName("url");
		String tmp = nList.item(0).getTextContent();
		
		Node nNode = nList.item(0);

		this.url = tmp;
		
		nList = doc.getElementsByTagName("unknownNode");
		
		for (int temp = 0; temp < nList.getLength(); temp++) {
			
			paras.add(nList.item(temp).getTextContent());
	 
		}
		
		System.out.println();
	}
	
	public Gson getGson() {
		return gson;
	}
	public JsonObject getObj() {
		return obj;
	}
	public JsonParser getParser() {
		return parser;
	}
	public String getPath() {
		return path;
	}
	public String getURL() {
		return this.url;	
	}
	public ArrayList<String> getParagraphs() {
//		ArrayList<String> paras = new ArrayList<String>();
//		JsonArray jArray = obj.get("paragraphs").getAsJsonArray(); 
//		if (jArray != null) { 
//		   for (int i=0;i<jArray.size();i++){ 
//		    paras.add(jArray.get(i).toString());
//		   } 
//		}
		return paras;	
	}
}
