package fr.io.block;

import cpw.mods.fml.common.registry.GameRegistry;
import fr.io.lib.RefStrings;
import net.minecraft.block.Block;
import net.minecraft.block.material.Material;
import net.minecraft.creativetab.CreativeTabs;
import net.minecraft.tileentity.TileEntity;

import cpw.mods.fml.common.Optional;
public class SocketIOAdapter {

	
	public static void mainRegistry(){
		initializeBlock();
		registerItem();
	}
	
	public static Block IOAdapter;
	
	public static void initializeBlock(){
		IOAdapter = new SocketAdapter(Material.iron)
				.setBlockName("Socket IO Adapter")
				.setCreativeTab(CreativeTabs.tabRedstone)
				.setBlockTextureName(RefStrings.MODID + ":socketio");
	}
	
	public static void registerItem(){
		GameRegistry.registerTileEntity(SocketTileEntity.class, "socket_tile_entity");
		GameRegistry.registerBlock(IOAdapter, IOAdapter.getUnlocalizedName());
	}
	
	
	
}
