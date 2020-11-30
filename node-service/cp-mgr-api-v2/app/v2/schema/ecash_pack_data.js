/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ecash_pack_data', {
    ecash_pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_size: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reatailer_ecash_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    reatiler_ecash_value: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    ff_ecash_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    ff_ecash_value: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'ecash_pack_data'
  });
};
