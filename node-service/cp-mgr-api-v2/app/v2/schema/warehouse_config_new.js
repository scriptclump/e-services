/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('warehouse_config_new', {
    wh_loc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'legalentity_warehouses',
        key: 'le_wh_id'
      }
    },
    wh_location: {
      type: DataTypes.STRING(225),
      allowNull: true
    },
    wh_location_types: {
      type: DataTypes.INTEGER(7),
      allowNull: true
    },
    parent_loc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    sort_order: {
      type: DataTypes.INTEGER(5),
      allowNull: true,
      defaultValue: '0'
    },
    res_prod_grp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    pref_prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    bin_type_dim_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    length: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    breadth: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    height: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    lenght_UOM: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    x: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    y: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    z: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'warehouse_config_new'
  });
};
