/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hsn_test', {
    hsn_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    hsn_code: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    tax_per: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'hsn_test'
  });
};
