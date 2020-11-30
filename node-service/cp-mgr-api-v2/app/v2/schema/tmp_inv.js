/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('tmp_inv', {
    sku_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    Pid: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    atp: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'tmp_inv'
  });
};
