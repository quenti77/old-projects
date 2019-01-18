package fr.quenti.model;

/**
 * Représente un plugin
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class Plugin {
	
	private int id;
	private String name;
	private String version;
	private int action;
	private String link;
	private String versionOff;
	
	public Plugin(int id, String name, String version, int action, String link, String versionOff) {
		this.id = id;
		this.name = name;
		this.version = version;
		this.action = action;
		this.link = link;
		this.versionOff = versionOff;
	}

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getVersion() {
		return version;
	}

	public void setVersion(String version) {
		this.version = version;
	}

	public int getAction() {
		return action;
	}

	public void setAction(int action) {
		this.action = action;
	}

	public String getLink() {
		return link;
	}

	public void setLink(String link) {
		this.link = link;
	}

	public String getVersionOff() {
		return versionOff;
	}

	public void setVersionOff(String versionOff) {
		this.versionOff = versionOff;
	}

}
