/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_negative_ecash', {
    User ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Business Name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    Legal ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Creditlimit: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    Cashback: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    Applied Cashback: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    }
  }, {
    tableName: 'vw_negative_ecash'
  });
};
