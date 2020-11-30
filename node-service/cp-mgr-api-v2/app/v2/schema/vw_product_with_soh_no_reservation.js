/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_product_with_soh_no_reservation', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_product_with_soh_no_reservation'
  });
};
