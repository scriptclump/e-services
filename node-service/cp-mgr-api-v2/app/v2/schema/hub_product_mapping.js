/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hub_product_mapping', {
    hbm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    scope_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    scope_type: {
      type: DataTypes.ENUM('DC','HUB','SPOKE','BEAT'),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ref_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ref_type: {
      type: DataTypes.ENUM('brands','manufacturers'),
      allowNull: true
    }
  }, {
    tableName: 'hub_product_mapping'
  });
};
