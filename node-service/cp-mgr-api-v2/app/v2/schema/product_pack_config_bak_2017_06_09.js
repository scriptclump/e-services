/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_pack_config_bak_2017_06_09', {
    pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    capacity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    no_of_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_sku_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    pack_code_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    inner_pack_count: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    child_pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    star: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '140003'
    },
    lbh_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    length: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000000'
    },
    breadth: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000000'
    },
    height: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000000'
    },
    weight_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    weight: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    vol_weight_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    vol_weight: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    stack_height: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    pack_material: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    is_cratable: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true,
      defaultValue: '1'
    },
    palletization: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    pallet_capacity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_sellable: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'product_pack_config_bak_2017_06_09'
  });
};
