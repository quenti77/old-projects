package fr.plugin.data;

public class Task {

	private int id;
	private String name;
	private String desc;
	private String author;
	private boolean checked;
	
	public Task(int id, String name, String desc, String author, boolean checked) {
		this.id = id;
		this.name = name;
		this.desc = desc;
		this.author = author;
		this.checked = checked;
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

	public String getDesc() {
		return desc;
	}

	public void setDesc(String desc) {
		this.desc = desc;
	}

	public String getAuthor() {
		return author;
	}

	public void setAuthor(String author) {
		this.author = author;
	}

	public boolean isChecked() {
		return checked;
	}

	public void setChecked(boolean checked) {
		this.checked = checked;
	}
	
	
}
