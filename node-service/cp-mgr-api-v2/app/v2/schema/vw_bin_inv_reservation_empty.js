/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_bin_inv_reservation_empty', {
    wh_location: {
      type: DataTypes.STRING(225),
      allowNull: true
    },
    pref_prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    bin_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_bin_inv_reservation_empty'
  });
};
